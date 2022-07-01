
<?php
require_once('../admin/functions/common_function.php');
require_once('../admin/include/conectdb.php');
require_once('../admin/include/config.inc.php');
require_once('../admin/include/my-functions.php');
date_default_timezone_set("Asia/Kolkata");
$fromDate= date('Y-m-d', strtotime($_GET['from_date']));
$toDate= date('Y-m-d', strtotime($_GET['to_date']));
$sdate = $_GET['from_date'];
$edate = $_GET['to_date'];
// $toDate=$_GET['toDate'];
 $query = "SELECT user_sales_order.*,user_sales_order_details.*,user_sales_order_details.case_qty as caseQty,catalog_view.*, retailer.*,location_view.*,dealer.name as dealerName,person.*,_retailer_outlet_type.outlet_type as outletType,dealer_code,_role.rolename as rolename,person.id as person_id FROM `user_sales_order` 
INNER JOIN user_sales_order_details ON user_sales_order_details.order_id = user_sales_order.order_id
INNER JOIN dealer ON dealer.id = user_sales_order.dealer_id
INNER JOIN retailer ON retailer.id = user_sales_order.retailer_id
INNER JOIN location_view ON location_view.l5_id = user_sales_order.location_id
INNER JOIN person ON user_sales_order.user_id = person.id 
INNER JOIN _retailer_outlet_type ON outlet_type_id = _retailer_outlet_type.id
INNER JOIN _role ON _role.role_id = person.role_id
INNER JOIN catalog_view ON catalog_view.product_id = user_sales_order_details.product_id
WHERE user_sales_order.date >= '$fromDate' AND user_sales_order.date <= '$toDate'";
// WHERE date_format(user_sales_order.date,'%d/%m/%Y') >= '$sdate' AND date_format(user_sales_order.date,'%d/%m/%Y') <= '$edate'";
$runQuery = mysqli_query($dbc,$query);
$num = mysqli_num_rows($runQuery);
$dataCount=array();
 // h1($num);
if($num > 0){
    $output .="S.No,Zone,AREA,State,RS Code,RS Name,OutletId,Outlet,Class,Type,Beat,User,Emp Id,Designation,SO,ASM,Order Date,Order Time,Brand,Product,SKU,Cases/Bags,Pcs,Weight(KG),Value";
    $output .="\n";
    $i=1;
while($value = mysqli_fetch_assoc($runQuery))
{
    // pre($value); exit;
    // 0=>none,1=>Platinum,2=>Diamond,3=>Gold,4=>Silver,5=>semi-ws,6=>ws
    switch($value['class'])
    {
        case 0: $class = "None"; break; 
        case 1: $class = "Platinum"; break; 
        case 2: $class = "Diamond"; break; 
        case 3: $class = "Gold"; break; 
        case 4: $class = "Silver"; break; 
        case 5: $class = "semi-ws"; break; 
        case 6: $class = "ws"; break; 
    }
    $contact_per_name = str_replace(",","|",$value['contact_per_name']);
    $fname = str_replace(",","|",$value['first_name']);
    $lname = str_replace(",","|",$value['last_name']);
    $l4_name = str_replace(",","|",$value['l4_name']);
    $dealerName = str_replace(",","|",$value['dealerName']);
    $l1_name = str_replace(",","|",$value['l1_name']);
    $l3_name = str_replace(",","|",$value['l3_name']);
    $l5_name = str_replace(",","|",$value['l5_name']);
    $l2_name = str_replace(",","|",$value['l2_name']);
    $name = str_replace(",","|",$value['name']);
    $email = str_replace(",","|",$value['email']);
    $address = str_replace(",","|",$value['address']);
    $track_address = str_replace(",","|",$value['track_address']);
    $product_name = str_replace(",","|",$value['product_name']);
$productValue = $value['quantity']*$value['rate'];
$person_id = $value['person_id'];
$person_id_senior = $value['person_id_senior'];
if($value['role_id'] == 46)
{
    $soId = $person_id;
    $soSeniorId = $value['person_id_senior'];
    $soSeniorRole = $value['role_id'];
    $soname = $fname." ".$lname;
}
else
{
   $query = "SELECT id,person_id_senior,CONCAT_WS(' ',first_name,middle_name,last_name) as personName, role_id FROM person WHERE id = '$person_id_senior' LIMIT 1";
    $run = mysqli_query($dbc,$query);
    $raw = mysqli_fetch_assoc($run);
// print_r($raw); exit;
    $soId = $raw['id'];
    $soSeniorId = $raw['person_id_senior'];
    $soname = $raw['personName'];
    $senorRoleId = $raw['role_id'];
}
  
  $asmquery = "SELECT id,person_id_senior,CONCAT_WS(' ',first_name,middle_name,last_name) as personName, role_id FROM person WHERE id = '$soSeniorId' LIMIT 1";
$asmrun = mysqli_query($dbc,$asmquery);
$asmraw = mysqli_fetch_assoc($asmrun);
$asmId = $asmraw['id'];
$asmSeniorId = $asmraw['person_id_senior'];
$asmname = $asmraw['personName'];
$asmsenorRoleId = $asmraw['role_id'];

                        $output .=$i.',';
                        $output .=$l1_name.',';
                        $output .=$l2_name.',';
                        $output .=$l3_name.',';
                        $output .=$value['dealer_code'].',';
                        $output .=$dealerName.',';
                        $output .=$value['retailer_code'].',';
                        $output .=$name.',';
                        $output .=$class.',';
                        $output .=$value['outletType'].',';
                        $output .=$l5_name.',';
                        $output .=$fname." ".$lname.',';                                                
                        $output .=$value['emp_code'].',';
                        $output .=$value['rolename'].',';

                        $output .= $soname.',';
                        $output .= $asmname.',';
                        $output .=$value['date'].',';
                        $output .=$value['time'].',';

                        /// PRODUCT PENDING


                        $output .=$value['c1_name'].',';
                        $output .=$value['c2_name'].',';
                        $output .=$product_name.',';
                       
                        $output .=$value['caseQty'].',';
                        $output .=$value['quantity'].',';
                        $output .=$value['weight'].',';
                        $output .=$productValue.',';

                       
                        $output .="\n";
                        $i++;
             
                        $dataCount=1;
        }
        // echo (count($dataCount));die;
        // WRITE FILE
        if($dataCount > 0){
            $data = $output;
            $filename = date(YmdHis).'.csv';
            $path='sale_data/';
            $file1 = fopen($path.$filename, 'wb');
            $f_r = fwrite($file1, $data);
            $downloadPath = 'sale_data/'.$filename;
            header('Content-type: application/csv');
            header("Content-disposition: attachment; filename= '$filename'");
            header("location: $downloadPath");
        }
      
      
}

