<?php
ini_set('max_execution_time', 300);
require_once('../../../admin/functions/common_function.php');
require_once('../../../admin/include/conectolddb.php');
require_once('../../../admin/include/config.inc.php');
require_once('../../../admin/include/my-functions.php');
$filter=$_GET['filter'];
$dealer_id=$_GET['dealerId'];
$rate= array();
$quantity=  array();
$amount= array();


 // $q="SELECT user_sales_order_details.id,l4_name as headquarter,l3_name as terr,CONCAT_WS(' ',`first_name`,`middle_name`,`last_name`) as `person_fullname`,
 // _role.rolename as `designation`,person.mobile ,( SELECT CONCAT_WS(' ',`first_name`,`middle_name`,`last_name`) as seniorname FROM person as p where p.id=person.person_id_senior) AS seniorname,DATE_FORMAT(user_sales_order.`date`,'%d-%m-%Y') AS sale_date,user_sales_order.order_id,REPLACE (`dealer`.`name`, '".'"'."', '') as `dealername`,REPLACE (`retailer`.`name`, '".'"'."', '') as `retailername`,REPLACE (`location_view`.`l5_name`, '".'"'."', '') as `beat`,
 // c1_name ,c2_name,c3_name,product_name,`user_sales_order_details`.`rate` as `rate`,`user_sales_order_details`.`quantity` as `quantity`,(rate*quantity) AS `amount` FROM `user_sales_order`
 //  LEFT JOIN user_sales_order_details ON user_sales_order_details.order_id=user_sales_order.order_id 
 //  INNER JOIN catalog_view ON catalog_view.product_id=user_sales_order_details.product_id 
 //  INNER JOIN person ON person.id=user_sales_order.user_id 
 //  INNER JOIN _role ON _role.role_id=person.role_id 
 //  LEFT JOIN dealer ON dealer.id=user_sales_order.dealer_id 
 //  LEFT JOIN location_view ON location_view.l5_id=user_sales_order.location_id 
 //  INNER JOIN retailer ON retailer.id=user_sales_order.retailer_id 
 //  WHERE $filter  group by catalog_view.product_id,user_sales_order_details.order_id  ORDER BY user_sales_order.date ASC";


   $q = "select stock.id as id,itemcode,catalog_product.name as product_name,stock.mrp,stock.dealer_rate,stock.qty as quantity,stock.batch_no,DATE_FORMAT(stock.mfg, '%d/%m/%Y') AS date  from stock
   		inner join catalog_product on catalog_product.id = stock.product_id
   		where stock.dealer_id = $dealer_id"; 
 
  
 // h1($q);exit;
 $output .='S.No,Item Code,Product Name,MRP,Rate,Quantity(In Pcs.),Batch No,Mfg Date(dd/mm/yyyy)';
 $output .="\n";
$res_q = mysqli_query($dbcold, $q);
$J=1;
while($row_q = mysqli_fetch_array($res_q)){
$columns_total=19; 



for ($i = 0; $i < $columns_total; $i++) {

$output .='"'.$row_q["$i"].'",';
}
 $output .="\n";

$J++;
 }

 // $output.=$J;
$filename = "PreviousStock.csv";
header('Content-type: application/csv');
header('Content-Disposition: attachment; filename='.$filename);

echo $output;
exit;
