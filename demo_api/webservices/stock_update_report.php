<?php
ini_set("memory_limit",-1);
ini_set("max_execution_time",-1);
require_once('../admin/functions/common_function.php');
require_once('../admin/include/conectdb.php');
require_once('../admin/include/config.inc.php');
require_once('../admin/include/my-functions.php');
global $dbc;

  $q = "INSERT INTO `daily_stock` ( `product_id` , `batch_no` , `rate` , `dealer_rate` , `mrp` , `person_id` , `csa_id` , `dealer_id` , `qty` , `salable_damage` , `nonsalable_damage` , `remaining` , `mfg` , `expire` , `date` , `last_updated` , `pr_rate` , `company_id` , `action` , `sync_status` )
SELECT `product_id` , `batch_no` , `rate` , `dealer_rate` , `mrp` , `person_id` , `csa_id` , `dealer_id` , `qty` , `salable_damage` , `nonsalable_damage` , `remaining` , `mfg` , `expire` , `date` , 'NOW()' , `pr_rate` , `company_id` , `action` , `sync_status`
FROM `stock` ";

 //h1($q);exit;
  $r=mysqli_query($dbc,$q);
  if($r){
    echo "Daily Stock Updated";
  }else{
    echo "Daily Stock Not Updated";
  }
   
?>
