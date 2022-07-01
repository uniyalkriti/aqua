
<?php
require_once('../admin/functions/common_function.php');
require_once('../admin/include/conectdb.php');
require_once('../admin/include/config.inc.php');
require_once('../admin/include/my-functions.php');

$fromDate=$_GET['fromDate'];
 $query = "SELECT DISTINCT retailer.id as retailer_id , retailer.*,location_view.*,dealer.name as dealerName,person.*,_retailer_outlet_type.outlet_type as outletType FROM `retailer` 
INNER JOIN dealer ON dealer.id = retailer.dealer_id 
INNER JOIN location_view ON location_view.l5_id = retailer.location_id 
LEFT JOIN person ON created_by_person_id = person.id 
INNER JOIN _retailer_outlet_type ON outlet_type_id = _retailer_outlet_type.id ORDER BY l1_name,l3_name ASC";
$runQuery = mysqli_query($dbc,$query);
$num = mysqli_num_rows($runQuery);
$dataCount=array();
   //h1($num);
if($num > 0){
    $output .="S.No,Retailer id,Retailer Name,Retailer Type,Retailer Category,Retailer Date & Time,Owner Name,Retailer Status,User Name,Belt/Tert/Area/Region,Dealer Town,dealer code,Dealer Name,Zone,State,State Name,Beat Name,Email,Mobile,Address,Tracking Address,Pin No";
    $output .="\n";
    $i=1;
while($value = mysqli_fetch_assoc($runQuery))
{
    // pre($value);
    // 0=>none,1=>Platinum,2=>Diamond,3=>Gold,4=>Silver,5=>semi-ws,6=>ws
    switch($value['class'])
    {
        case 0: $class = "None"; break; 
        case 1: $class = "Platinum"; break; 
        case 2: $class = "Diamond"; break; 
        case 3: $class = "Gold"; break; 
        case 4: $class = "Silver"; break; 
        case 5: $class = "Semi-WS"; break; 
        case 6: $class = "WS"; break; 
    }
                        $class_name=$value['class']==0?'None':$class;

                        $contact_per_name = str_replace(",","|",$value['contact_per_name']);
                        $retailer_name = !empty($value['name'])?str_replace(",","|",$value['name']):'NA';
                        $fname = str_replace(",","|",$value['first_name']);
                        $lname = str_replace(",","|",$value['last_name']);
                        $l4_name = str_replace(",","|",$value['l4_name']);
                        $dealerName = str_replace(",","|",$value['dealerName']);
                        $l1_name = str_replace(",","|",$value['l1_name']);
                        $l3_name = str_replace(",","|",$value['l3_name']);
                        $l5_name = str_replace(",","|",$value['l5_name']);
                        $beat_name=str_replace("\r\n"," ",$l5_name);
                        $email = str_replace(",","|",$value['email']);
                        $address = str_replace(",","|",$value['address']);
                        $track_address = str_replace(",","|",$value['track_address']);

                        $output .=$i.',';
                        $output .=$value['retailer_id'].',';
                        $output .=$retailer_name.',';
                        $output .=$value['outletType'].',';
                        $output .=$class_name.',';
                        $output .=$value['created_on'].',';
                        $output .=$contact_per_name.',';
                        $output .=$value['retailer_status'].',';

                        $output .=$fname." ".$lname.',';
                        $output .=$value['region_txt'].',';

                        $output .=$l4_name.',';
                        $output .=$value['dealer_code'].',';
                        $output .=$dealerName.',';
                        $output .=$l1_name.',';
                        $output .=$value['l3_id'].',';
                        $output .=$l3_name.',';
                        $output .=$beat_name.',';
                        $output .=$email.',';
                        $output .=$value['landline'].',';
                        $output .=$address.',';
                        $output .=$track_address.',';
                        $output .=$value['pin_no'].',';
                       
                       
                        $output .="\n";
                        $i++;
             
                        $dataCount=1;
        }
        // echo (count($dataCount));die;
        // WRITE FILE
        if($dataCount > 0)
        {
            $data = $output;
            $filename = date(YmdHis).'.csv';
            $path='schedule_files/';
            $file1 = fopen($path.$filename, 'wb');
            $f_r = fwrite($file1, $data);
            $downloadPath = 'schedule_files/'.$filename;
            header('Content-type: application/csv');
            header("Content-disposition: attachment; filename= '$filename'");
            header("location: $downloadPath");
        }
      
      
}

