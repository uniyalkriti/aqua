
<?php
require_once('../admin/functions/common_function.php');
require_once('../admin/include/conectdb.php');
require_once('../admin/include/config.inc.php');
require_once('../admin/include/my-functions.php');
if(isset($_GET['month'])) $month = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['month']))); else $month = 0;
if(isset($_GET['junior_id'])) $junior_id = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['junior_id']))); else $junior_id = 0;
$myobj=new mtp();
    $data_info = array();
    $total_sale = array();
    $final_data_info = array();
   $q = "SELECT working_date,working_status_id,total_calls,total_sales,travel_mode,`to` as to_beat_id,person_id,CONCAT_WS(' ',first_name,middle_name,last_name) as person_fullname,travel_distance,(SELECT DISTINCT csa.csa_name FROM csa WHERE csa.c_id=mtp.ss_id) as ss, "
      . " (SELECT DISTINCT l5.name FROM location_5 l5 WHERE l5.id=mtp.from) as from_beat_name,(SELECT DISTINCT dealer.name FROM dealer WHERE dealer.id=mtp.dealer_id) as dealer FROM monthly_tour_program mtp "
      . " INNER JOIN person ON person.id=mtp.person_id WHERE DATE_FORMAT(`working_date`,'%m%Y')='$month' AND senior_approved=0 AND person_id='$junior_id' GROUP BY mtp.id ORDER BY working_date ASC";
      //echo  $q;die;
    list($opt, $rs) = run_query($dbc, $q, 'multi');
 if($opt) 
 {
    while($row = mysqli_fetch_assoc($rs))
    {
        $data_info['working_date'] = isset($row['working_date'])?$row['working_date']:'';
        $data_info['working_status_id'] = isset($row['working_status_id'])?$row['working_status_id']:'';
        $data_info['dealer_id'] = isset($row['dealer'])?$row['dealer']:'';
        $data_info['total_calls'] = isset($row['total_calls'])?$row['total_calls']:'';
        $data_info['total_sales'] = isset($row['total_sales'])?$row['total_sales']:'';
        $data_info['ss_id'] = isset($row['ss'])?$row['ss']:'';
        $data_info['travel_mode'] = isset($row['travel_mode'])?$row['travel_mode']:'';
        $data_info['person_id'] = isset($row['person_id'])?$row['person_id']:'';
        $data_info['person_fullname'] = isset($row['person_fullname'])?$row['person_fullname']:'';
        $data_info['travel_distance'] = isset($row['travel_distance'])?$row['travel_distance']:'';
        $data_info['from_beat_name'] = isset($row['from_beat_name'])?$row['from_beat_name']:'';
        $data_info['to_beat_name'] =$myobj->get_m_location_list($row['to_beat_id']); 
        $final_data_info[] = $data_info;
    }
 } // if($opt)  end here
 //$total_sale_value = array_sum($total_sale['total_sale_value']);
 $final_array = array("result"=>$final_data_info);	
 $data = json_encode($final_array);
 echo $data;


?>