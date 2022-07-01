
<?php
require_once('../admin/functions/common_function.php');
require_once('../admin/include/conectdb.php');
require_once('../admin/include/config.inc.php');
require_once('../admin/include/my-functions.php');
date_default_timezone_set("Asia/Kolkata");
$fromDate= date('Y-m-d', strtotime($_GET['from_date']));
$toDate= date('Y-m-d', strtotime($_GET['to_date']));
$user = $_GET['user'];
// $edate = $_GET['to_date'];
// $toDate=$_GET['toDate'];
 $query = "SELECT dealer_location_rate_list.location_id as beatId,first_name,last_name,l5_name,l4_name,l3_name,l2_name,l1_name,dealer.name as dealer_name FROM person
INNER JOIN dealer_location_rate_list ON dealer_location_rate_list.user_id = person.id
INNER JOIN dealer ON dealer.id = dealer_location_rate_list.dealer_id
INNER JOIN location_view ON location_view.l5_id = dealer_location_rate_list.location_id";

if(!empty($user))
{
$query .= " WHERE person.id = '$user'";
}
$query .= " ORDER BY first_name";
// echo $query;
// WHERE date_format(user_sales_order.date,'%d/%m/%Y') >= '$sdate' AND date_format(user_sales_order.date,'%d/%m/%Y') <= '$edate'";
$runQuery = mysqli_query($dbc,$query);
$num = mysqli_num_rows($runQuery);
$dataCount=array();
 // h1($num);
if($num > 0){
    $output .="S.No,USER NAME,ZONE/REGION,STATE,TOWN/CITY,DEALER NAME,BEAT NAME";
    $output .="\n";
    $i=1;
while($value = mysqli_fetch_assoc($runQuery))
{
    // pre($value); exit;
   
    $fname = str_replace(",","|",$value['first_name']);
    $lname = str_replace(",","|",$value['last_name']);
    $l4_name = str_replace(",","|",$value['l4_name']);
    $dealerName = str_replace(",","|",$value['dealer_name']);
    $l1_name = str_replace(",","|",$value['l1_name']);
    $l3_name = str_replace(",","|",$value['l3_name']);
    $l5_name = str_replace(",","|",$value['l5_name']);
    $l2_name = str_replace(",","|",$value['l2_name']);
   


                        $output .=$i.',';
                        $output .=$fname.' '.$lname.',';
                        $output .=$l1_name.'/'.$l2_name.',';
                        $output .=$l3_name.',';
                        $output .=$l4_name.',';
                        $output .=$dealerName.',';
                        $output .=$l5_name.',';
                        $output .="\n";
                        $i++;
             
                        $dataCount=1;
        }
        // echo (count($dataCount));die;
        // WRITE FILE
        if($dataCount > 0){
            // $data = $output;
            $filename = 'Dealer'.date(YmdHis).'.csv';
            // $filename = "Retailer_data.csv";
header('Content-type: application/csv');
header('Content-Disposition: attachment; filename='.$filename);
echo $output;
            // $path='sale_data/';
            // $file1 = fopen($path.$filename, 'wb');
            // $f_r = fwrite($file1, $data);
            // $downloadPath = 'sale_data/'.$filename;
            // header('Content-type: application/csv');
            // header("Content-disposition: attachment; filename= '$filename'");
            // header("location: $downloadPath");
        }
      
      
}

