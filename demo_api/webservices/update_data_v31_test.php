<?php
include "../admin/include/generate_data.php";
require_once('../admin/functions/common_function.php');
require_once('../admin/include/conectdb.php');
require_once('../admin/include/config.inc.php');
require_once('../admin/include/my-functions.php');
error_reporting(1);

$myobj = new mtp();
$imei = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['imei'])));
$uname = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['uname'])));
$pass = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['pass'])));
$v_code = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['v_code'])));
$v_name = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['v_name'])));
$rest = substr($imei, -5) * 1; // multiplying by 1 for termination starting zero values
#Don't enable it otherwise user can login without product
//$final_catalog_product_details=[];

$query_person_login = "SELECT * FROM `person_login` INNER JOIN person ON person_login.person_id = person.id 
AND person_username = '$uname' AND person_password = AES_ENCRYPT('$pass', '" . EDSALT . "') 
AND imei_number = '$imei' AND person_status = '1' ORDER BY person_username ASC";
// h1($query_person_login);die;
$user_qry = mysqli_query($dbc, $query_person_login);
$user_res = mysqli_fetch_assoc($user_qry);
$get_user_id = $user_res['id'];
$loggedInTownArr=[];
if (!empty($user_res['state_id']))
{
    $logged_query="SELECT * FROM location_4 WHERE location_3_id='$user_res[state_id]' ORDER BY location_4.name ASC";
    $logged_query_data = mysqli_query($dbc, $logged_query) or die(mysqli_error($dbc));
    if (mysqli_num_rows($logged_query_data) > 0) {
        while ($lq = mysqli_fetch_assoc($logged_query_data)) {
            $logArr['id'] = $lq['id'];
            $logArr['name'] = $lq['name'];
            $loggedInTownArr[] = $logArr;
        }
    }
}
$dir=explode('/',getcwd());
$kk=count($dir)-1;
$actual_link = "http://$_SERVER[HTTP_HOST]/".$dir[$kk-1].'/'.$dir[$kk].'/mobile_images/profile/';
$person_image=$actual_link.$user_res['person_image'];
mysqli_query($dbc, "update person SET version_code_name='Version: $v_code / $v_name' Where id='$user_res[person_id]'");
mysqli_query($dbc, "update person_login SET last_mobile_access_on=NOW(), app_type='SFA' Where person_id='$get_user_id'");
$company_id = 1;
if ($user_qry && mysqli_num_rows($user_qry) > 0) {
//echo $id;
//function signin_12Version_data_generate($get_user_id){
//echo "ANKUSH";
// echo $get_user_id;
    global $dbc;

    $myobj = new mtp();
    /*$imei = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['imei'])));
    $uname = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['uname'])));
    $pass = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['pass'])));*/
    $id = mysqli_real_escape_string($dbc, trim(stripslashes($get_user_id)));

    #MTD Start
    $mtd_target=0;
    $mtd_achievement=0;
    if (!empty($id)) {
        $day=date('d',strtotime('now'));
//         $my_query = "SELECT SUM(rd) as total_rd
// FROM monthly_tour_program
// WHERE MONTH(`working_date`) = MONTH(CURRENT_DATE())
// AND YEAR(`working_date`) = YEAR(CURRENT_DATE())
// AND DAY(`working_date`)>='1' AND DAY(`working_date`)<='$day' AND person_id='$id'";
        $curdates=date('Y-m-d');
        $onedates=date('Y-m')."-01";
 $my_query = "SELECT SUM(rd) as total_rd
FROM monthly_tour_program
WHERE 
 DATE_FORMAT(`working_date`,'%Y-%m-%d')>='$onedates' AND DATE_FORMAT(`working_date`,'%Y-%m-%d')<='$curdates' AND person_id='$id'";

        $ach = "SELECT SUM(amount) as total_achievement
FROM user_sales_order
WHERE MONTH(`date`) = MONTH(CURRENT_DATE())
AND YEAR(`date`) = YEAR(CURRENT_DATE())
AND DAY(`date`)>='1' AND DAY(`date`)<='$day' AND user_id='$id'";
        $query_run = mysqli_query($dbc, $my_query);
        $ach_run = mysqli_query($dbc, $ach);
        $fetch = mysqli_fetch_assoc($query_run);
        $fetch2 = mysqli_fetch_assoc($ach_run);
        $percentage_ratio=0;
        if (!empty($fetch2['total_achievement']) && !empty($fetch['total_rd']))
        {
            $percentage_ratio=($fetch2['total_achievement']/$fetch['total_rd'])*100;
        }
        $mtd_target=!empty($fetch['total_rd'])?$fetch['total_rd']:0;
        $mtd_achievement=!empty($fetch2['total_achievement'])?$fetch2['total_achievement']:0;

    }
    #MTD End

    $rest = substr($imei, -5) * 1;
// multiplying by 1 for termination starting zero values
/////////////////////ANK////////////////////
    $query_person_login = "SELECT *,person.state_id,person.mobile,CONCAT_WS(' ',first_name,middle_name,last_name) as full_person_name FROM `person_login` INNER JOIN person ON person_login.person_id = person.id AND person.id='$id' AND person_status = '1' ORDER BY person_username ASC";
//h1($query_person_login);
    $user_qry = mysqli_query($dbc, $query_person_login);
//$user_res= mysqli_fetch_assoc($user_qry);
    $company_id = 1;
    if ($user_qry && mysqli_num_rows($user_qry) > 0) {

//$tracking = $myobj->tracking_mobile_data($filter="person_username = '$uname' AND person_password = '$pass' AND imei_number = '$imei' AND person_status = '1' ", $records = '', $orderby='ORDER BY person_username ASC');
        $tracking_constant = array();
//>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>Code Was Not working Temporary Commented
//$tracking = $myobj->get_constant_datas_list($filter = "", $records = '', $orderby = '');
//>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
        $tracking = $tracking[1];
        $tracking_constant[] = $tracking;
//pre($tracking_constant);
        $tracking_interval = explode(',', $tracking['tracking_intervals']);

        $inc = 1;
        $role_arr=[];
        $time = array();
        $time_info = array();
        $stimedetails = array();
        $timedetailsinfo = array();
        $timedetailsinfo[] = array("time1" => $tracking['tracking_time_start']);
        $timedetailsinfo[] = array("time2" => $tracking['tracking_time_end']);
        $stimedetails_info = $timedetailsinfo;
//pre($tracking_interval);
        foreach ($tracking_interval as $key => $value) {
            $time[] = array("tm_time$inc" => $value);
            $inc++;
        }

//$time[] = array("tm_time$inc"=>20);
        $time_info = $time;

//require_once('query.php');
// default query for insert data in catalog_product_list
        $essential = array();
        $dealer_info = array();
        $dealer_array = array();
        $dealer_location = array();
        $final_dealer_location_details = array();
        $beat_array = array();
//        $final_dealer_details = array();
        $retailer_info = array();
        $final_retailer_details = array();
        $beat_info = array();
        $final_beat_details = array();
        $brand_info = array();
        $final_brand_details = array();
        $size_info = array();
        $final_size_details = array();
        $category_info = array();
        $final_category_details = array();
        $outlet = array();
        $final_outlet_details = array();
        $outlet_categories = array();
        $ownership = array();
        $final_ownership_details = array();
        $experience = array();
        $final_experience_details = array();
        $retailer_gift = array();
        $final_retailer_gift = array();
        $travel = array();
        $final_travel_deatails = array();
        $final_working_deatails = array();
        $working = array();
        $mstatusarray = array();
        $final_mstatus_details = array();
        $competator_info = array();
        $leave = array();
        $scheme = array();
        $stock = array();
        $mtp = array();
        $complaint = array();
        $scheme_inbuilt = array();
        $gift = array();
        $retailer_target = array();
        $person_info = array();
        $final_damage_product_details = array();
        $final_isr_array = array();
        $isr_array = array();
        $user_for_manual_attendance = array();
        $final_product_recommended = array();
        $user_retrailer_incrementid = 0;
        $constant_values = "";
        $daily_schedule_details = array();
        $payment_mode = array();
        $ww = array();
        $tracking_data=[];
        $webview_status=0;
        $webview_url='';
//        $webview=[];
//        #webview code
//        $webdata=array('web_report_status'=>0,'web_url'=>'www.google.com');
//        $webview=$webdata;


        #working status
        $nonq = "SELECT * FROM `_working_with` WHERE status=1";
        $non_sos = mysqli_query($dbc, $nonq) or die(mysqli_error($dbc));
        if (mysqli_num_rows($non_sos) > 0) {
            while ($xx = mysqli_fetch_assoc($non_sos)) {
                $aer['id'] = $xx['id'];
                $aer['name'] = $xx['name'];
                $ww[] = $aer;
            }
        }
        function getSeniorList($id,$dbc,$j=0)
        {
            if ($id==1){return $_SESSION['idArr'];}

            $query = "SELECT a.id as id,CONCAT_WS(' ',a.first_name,a.middle_name,a.last_name) as name from person a INNER JOIN  person b ON a.id=b.person_id_senior WHERE b.id=$id";
            $qr = mysqli_query($dbc, $query) or die(mysqli_error($dbc));
            if (mysqli_num_rows($qr) > 0) {
                while ($row = mysqli_fetch_assoc($qr)) {
                    if ($row['id']>1)
                    {
                    $i=['id'=>$row['id'],'name'=>$row['name']];
                    $_SESSION['idArr'][$j]=$i;
                    $j++;

                        getSeniorList($row['id'],$dbc,$j);
                    }
                }
            }

            return $_SESSION['idArr'];
        }
        $ox1=getSeniorList($id,$dbc);
        $test['senior']=!empty($ox1)?$ox1:[];

        function getJuniorList1($id,$dbc,$j=0)
        {
            if ($id==1){return $_SESSION['idArr2'];}

            $query = "SELECT b.id as id,CONCAT_WS(' ',b.first_name,b.middle_name,b.last_name) as name from person b INNER JOIN  person a ON a.id=b.person_id_senior WHERE b.person_id_senior=$id";
            $qr = mysqli_query($dbc, $query) or die(mysqli_error($dbc));
            if (mysqli_num_rows($qr) > 0) {
                while ($row = mysqli_fetch_assoc($qr)) {
                    $_SESSION['id'][$j]=$row['id'];
                    $i=['id'=>$row['id'],'name'=>$row['name']];
                    $_SESSION['idArr2'][$j]=$i;
                    $j++;
                    getJuniorList($row['id'],$dbc,$j);
                    // if ($id>1)
                    // {
                    //     getJuniorList($row['id'],$dbc,$j);
                    // }
                }
            }

            return $_SESSION['idArr2'];
        }

        function getJuniorList($id,$dbc,$j=0)
        {
            if ($id==1){return $_SESSION['idArr2'];}

            $query = "SELECT b.id as id,CONCAT_WS(' ',b.first_name,b.middle_name,b.last_name) as name from person b WHERE b.person_id_senior='$id'";
            $qr = mysqli_query($dbc, $query) or die(mysqli_error($dbc));
            if (mysqli_num_rows($qr) > 0) {
                while ($row = mysqli_fetch_assoc($qr)) {
                    $_SESSION['id'][$j]=$row['id'];
                    $i=['id'=>$row['id'],'name'=>$row['name']];
                    $_SESSION['idArr2'][$j]=$i;
                    $j++;
                    getJuniorList($row['id'],$dbc,$j);
                    // if ($id>1)
                    // {
                    //     getJuniorList($row['id'],$dbc,$j);
                    // }
                }
            }

            return $_SESSION['idArr2'];
        }
        
        // $ox2=getJuniorList($id,$dbc);
        // // print_r($_SESSION['idArr2']); exit;
        // $test['junior']=!empty($ox2)?$ox2:[];

        $colleArr=[0=>['id'=>'0','name'=>'SELF']];
        $collegue_count=1;
        recursivejuniorsName($id); 
        $juniors = array();
        $juniors = $_SESSION['resursivedata'];
        if($juniors==''){ $juniors=0; }
// print_r($_SESSION['resursivedata']); exit;
$_SESSION['resursivedata']='';
        //END OF RECURSIVE
        // foreach ($test['junior'] as $tx)
        foreach($juniors as $tx)
        {
            $colleArr[$collegue_count]=$tx;
            $collegue_count++;
        }

        recursiveseniorName($id);
        $seniors = $_SESSION['resursiveseniordata'];
        if($seniors==''){ $seniors=0; }
// print_r($_SESSION['resursiveseniordata']); exit;
$_SESSION['resursiveseniordata']='';
        // foreach ($test['senior'] as $tt)
        foreach($seniors as $tt)
        {
            $colleArr[$collegue_count]=$tt;
            $collegue_count++;
        }

        $colleague=$colleArr;


        #Show MTP approve for role
        $role_query="SELECT role_id,rolename from _role where rolename!='ISR'";
        $role_data=mysqli_query($dbc,$role_query) or die(mysqli_error($dbc));
        if (mysqli_num_rows($role_data)>0)
        {
            while ($xn=mysqli_fetch_assoc($role_data))
            {
                $rd['role_id']=$xn['role_id'];
                $rd['rolename']=$xn['rolename'];
                $role_arr[]=$rd;
            }
        }

        #Tracking constants
        $tracq = "SELECT * FROM `_tracking`";
        $tracq_sos = mysqli_query($dbc, $tracq) or die(mysqli_error($dbc));
        if (mysqli_num_rows($tracq_sos) > 0) {
            while ($xxl = mysqli_fetch_assoc($tracq_sos)) {
                $air['start'] = $xxl['start'];
                $air['end'] = $xxl['end'];
                $air['time_interval'] = $xxl['time_interval'];
                $tracking_data[] = $air;
            }
        }



        $query_constant_level = "SELECT * FROM `_constant` WHERE company_id=1";
        $run_constant_level = mysqli_query($dbc, $query_constant_level);
        $dconstant_data = mysqli_fetch_assoc($run_constant_level);
        $dconstant_data;
        if ($dconstant_data['constant_status'] == 1) {
// $constant_data = $dconstant_data;
            $constants_values['callwise_reporting_status'] = $dconstant_data['callwise_reporting_status'];
            $constants_values['dalilysales_summary_status'] = $dconstant_data['dalilysales_summary_status'];
            $constants_values['debug_on'] = $dconstant_data['debug_on'];
            $constants_values['track_at_sale'] = $dconstant_data['track_at_sale'];
            $constants_values['mtp_confirm_required'] = $dconstant_data['mtp_confirm_required'];
            $constants_values['mtp_extended_status'] = $dconstant_data['mtp_extended_status'];
            $constants_values['mtp_buffer_days'] = $dconstant_data['mtp_buffer_days'];
        } else {
            $constants_values['callwise_reporting_status'] = "";
            $constants_values['dailysales_summary_status'] = "";
            $constants_values['debug_on'] = "";
            $constants_values['track_at_sale'] = "";
            $constants_values['mtp_confirm_required'] = "";
            $constants_values['mtp_extended_status'] = "";
            $constants_values['mtp_buffer_days'] = "";
        }

//pre($constant_data);


        $schedule_array = array();
        $q_sch = "SELECT * FROM _daily_schedule ORDER BY id ASC";
        $r_sch = mysqli_query($dbc, $q_sch);
        if ($r_sch && mysqli_num_rows($r_sch) > 0) {
            while ($sch_fetch = mysqli_fetch_assoc($r_sch)) {
                $schedule_array['id'] = $sch_fetch['id'];
                $schedule_array['schedule_name'] = $sch_fetch['name'];
                $daily_schedule_details[] = $schedule_array;
            }
        }


        $qoutlet = "SELECT id, outlet_type FROM _retailer_outlet_type";
        $routlet = mysqli_query($dbc, $qoutlet);
        if ($routlet) {
            while ($doutlet = mysqli_fetch_assoc($routlet)) {
                $outlet['id'] = $doutlet['id'];
                $outlet['outlet_type'] = $doutlet['outlet_type'];
                $final_outlet_details[] = $outlet;
            }
        }
        #outlet category
        $dt=[];
        $query = "SELECT id, outlet_category FROM _retailer_outlet_category where status=1";
        $qq = mysqli_query($dbc, $query);
        if ($qq) {
            while ($dtt = mysqli_fetch_assoc($qq)) {
                $dt['id'] = $dtt['id'];
                $dt['outlet_category'] = $dtt['outlet_category'];
                $outlet_categories[] = $dt;
            }
        }

// here we get data from ownership type
        $qowner = "SELECT id, ownership_type FROM _dealer_ownership_type";
        $rowner = mysqli_query($dbc, $qowner);
        if ($rowner) {
            while ($downer = mysqli_fetch_assoc($rowner)) {
                $ownership['id'] = $downer['id'];
                $ownership['ownership_type'] = $downer['ownership_type'];
                $final_ownership_details[] = $ownership;
            }
        }

        $qexperiance = "SELECT id, experience FROM _field_experience";
        $rexperiance = mysqli_query($dbc, $qexperiance);
        if ($rexperiance) {
            while ($dexperiance = mysqli_fetch_assoc($rexperiance)) {
                $experience['id'] = $dexperiance['id'];
                $experience['name'] = $dexperiance['experience'];
                $final_experience_details[] = $experience;
            }
        }

        $qgift = "SELECT id, gift_name FROM _retailer_mkt_gift";
        $rgift = mysqli_query($dbc, $qgift);
        if ($rgift) {
            while ($dgift = mysqli_fetch_assoc($rgift)) {
                $retailer_gift['id'] = $dgift['id'];
                $retailer_gift['name'] = $dgift['gift_name'];
                $final_retailer_gift[] = $retailer_gift;
            }
        }

//*******************  NEW product_recommended_details *************************
        $qpr = "SELECT * FROM product_recommended_details WHERE view_status=1";
        $rqpr = mysqli_query($dbc, $qpr);
        if ($rqpr) {
            while ($dpqr = mysqli_fetch_assoc($rqpr)) {
                $data_pqr['id'] = $dpqr['id'];
                $data_pqr['quantity'] = $dpqr['quantity'];
                $data_pqr['from_date'] = $dpqr['from_date'];
                $data_pqr['to_date'] = $dpqr['to_date'];
                $final_product_recommended[] = $data_pqr;
            }
        }

        #Payment mode
        $pq = "SELECT mode,id FROM  _payment_modes WHERE status=1";
        $pqd = mysqli_query($dbc, $pq);
        if ($pqd) {
            while ($pay = mysqli_fetch_assoc($pqd)) {
                $pay_arr['id'] = $pay['id'];
                $pay_arr['mode'] = $pay['mode'];
                $payment_mode[] = $pay_arr;
            }
        }


        $qtravel = "SELECT id, mode FROM _travelling_mode";
        $rtravel = mysqli_query($dbc, $qtravel);
        if ($rtravel) {
            while ($dtravel = mysqli_fetch_assoc($rtravel)) {
                $travel['id'] = $dtravel['id'];
                $travel['mode'] = $dtravel['mode'];
                $final_travel_deatails[] = $travel;
            }
        }


        $qworking = "SELECT id,name,parent_id FROM _working_status where id !='9' order by sequence asc";
//echo $qworking;die;
        $rworking = mysqli_query($dbc, $qworking);
        if ($rworking) {
            while ($dworking = mysqli_fetch_assoc($rworking)) {
                $working['id'] = $dworking['id'];
                if ($dworking['parent_id'] == 0)
                    $working['name'] = $dworking['name'];
                else
                    $working['name'] = $dworking['name'] . '=>' . get_parent_child($dworking['parent_id']);
                $final_working_deatails[] = $working;
            }
// $final_working_deatails[] = array('id'=>0,'name'=>'select');
//pre($final_working_deatails);
        }

        $role = array();
        $final_role_deatails = array();
        $qrole = "SELECT role_id, rolename FROM _role";
        $rrole = mysqli_query($dbc, $qrole);
        if ($rrole) {
            while ($drole = mysqli_fetch_assoc($rrole)) {
                $role['id'] = $drole['role_id'];
                $role['name'] = $drole['rolename'];
                $final_role_deatails[] = $role;
            }
        }

//getting bank branch list
        $bank = array();
        $final_bank_list = array();
        $qbank = "SELECT id,branch_name FROM _bank_branch";
        $rbank = mysqli_query($dbc, $qbank) or die(mysqli_error($dbc));
        if ($rbank) {
            while ($res = mysqli_fetch_assoc($rbank)) {
                $bank['id'] = $res['id'];
                $bank['name'] = $res['branch_name'];
                $final_bank_list[] = $bank;
            }
        }

//user wise retailer max id for creating new retailer
//user wise retailer max id for creating new retailer
        $urincreid_qry = "SELECT max(id) as ret_id FROM retailer WHERE id LIKE '$rest%' ";
        $urincreid_res = mysqli_query($dbc, $urincreid_qry);
        $urincreid = mysqli_fetch_assoc($urincreid_res);
        $retailer_increment_id = $urincreid['ret_id']; //'346850000';
        if (empty($retailer_increment_id))
            $retailer_increment_id = $rest * 10000;


        $qry_cl_txt = "SELECT multiple_rate_list_status ,dealer_rate_list_status,catalog_level,location_level,dealer_level,retailer_level, "
            . "tracking_status,tracking_time_start,tracking_time_end,tracking_count,tracking_intervals,tracking_sleep_minutes,tracking_trials "
            . "FROM _constant ";
        $qry_cl = mysqli_query($dbc, $qry_cl_txt);
        $res_cl = mysqli_fetch_assoc($qry_cl);
        $catalog_level = $res_cl['catalog_level'];

        $row = mysqli_fetch_assoc($user_qry);
        $person_id = $row['id'];
        $emp_id = $row['emp_id'];

        $sss=$row['state_id'];
        $person_location = mysqli_query($dbc, "SELECT l1_id,l2_id,l1_name,l2_name from location_view where l3_id='$sss' limit 0,1 ");
        $pl = mysqli_fetch_assoc($person_location);
//        print_r($pl);die;
        $state_name = $pl['l2_name'];
        $zone_name = $pl['l1_name'];
        $sid = $pl['l2_id'];
        $zid = $pl['l1_id'];

        $dealer_state_id = $row['state_id'];
        $product_division = $row['product_division'];
        $person_email = $row['email'];
        $emp_code = $row['emp_code'];
        $pdd = mysqli_query($dbc, "SELECT dob,state_id,_role.rolename FROM `person_details` 
LEFT JOIN person on person.id=person_details.person_id 
LEFT JOIN _role on person.role_id=_role.role_id WHERE `person_id` = '$person_id'");
        $ro = mysqli_fetch_assoc($pdd);
        $dob = $ro['dob'];
        $state_id = $ro['state_id'];
        $role_name = $ro['rolename'];

        $role_id = $row['role_id'];
        $full_person_name = $row['full_person_name'];
        $person_contact = $row['mobile'];
        $state_id = $row['state_id'];

        #Location Data
        $loc1 = [];
        $loc2 = [];
        $loc3 = [];
        $loc4 = [];
        $loc5 = [];
        $inc = 0;
        $dlrl = mysqli_query($dbc, "SELECT location_view.* FROM `dealer_location_rate_list` 
LEFT JOIN `location_view` on location_view.l5_id=dealer_location_rate_list.location_id 
WHERE dealer_location_rate_list.user_id = '$person_id'");
        while ($dlrl_data = mysqli_fetch_assoc($dlrl)) {
            $loc5[$inc]['l4_id'] = $dlrl_data['l4_id'];
            $loc5[$inc]['l4_name'] = $dlrl_data['l4_name'];
            $loc5[$inc]['id'] = $dlrl_data['l5_id'];
            $loc5[$inc]['name'] = $dlrl_data['l5_name'];
            $inc++;
        }
        $inc = 0;
        $dlrl = mysqli_query($dbc, "SELECT location_view.* FROM `dealer_location_rate_list` 
LEFT JOIN `location_view` on location_view.l5_id=dealer_location_rate_list.location_id 
WHERE dealer_location_rate_list.user_id = '$person_id' GROUP BY l4_id ");
        while ($dlrl_data = mysqli_fetch_assoc($dlrl)) {
            $loc4[$inc]['l3_id'] = $dlrl_data['l3_id'];
            $loc4[$inc]['l3_name'] = $dlrl_data['l3_name'];
            $loc4[$inc]['id'] = $dlrl_data['l4_id'];
            $loc4[$inc]['name'] = $dlrl_data['l4_name'];
            $inc++;
        }
        $inc = 0;
        $dlrl = mysqli_query($dbc, "SELECT location_view.* FROM `dealer_location_rate_list` 
LEFT JOIN `location_view` on location_view.l5_id=dealer_location_rate_list.location_id 
WHERE dealer_location_rate_list.user_id = '$person_id' GROUP BY l3_id ");
        while ($dlrl_data = mysqli_fetch_assoc($dlrl)) {
            $loc3[$inc]['l2_id'] = $dlrl_data['l2_id'];
            $loc3[$inc]['l2_name'] = $dlrl_data['l2_name'];
            $loc3[$inc]['id'] = $dlrl_data['l3_id'];
            $loc3[$inc]['name'] = $dlrl_data['l3_name'];
            $inc++;
        }
        $inc = 0;
        $dlrl = mysqli_query($dbc, "SELECT location_view.* FROM `dealer_location_rate_list` 
LEFT JOIN `location_view` on location_view.l5_id=dealer_location_rate_list.location_id 
WHERE dealer_location_rate_list.user_id = '$person_id' GROUP BY l2_id ");
        while ($dlrl_data = mysqli_fetch_assoc($dlrl)) {
            $loc2[$inc]['l1_id'] = $dlrl_data['l1_id'];
            $loc2[$inc]['l1_name'] = $dlrl_data['l1_name'];
            $loc2[$inc]['id'] = $dlrl_data['l2_id'];
            $loc2[$inc]['name'] = $dlrl_data['l2_name'];
            $inc++;
        }
        $inc = 0;
        $dlrl = mysqli_query($dbc, "SELECT location_view.* FROM `dealer_location_rate_list` 
LEFT JOIN `location_view` on location_view.l5_id=dealer_location_rate_list.location_id 
WHERE dealer_location_rate_list.user_id = '$person_id' GROUP BY l1_id ");
        while ($dlrl_data = mysqli_fetch_assoc($dlrl)) {
            $loc1[$inc]['id'] = $dlrl_data['l1_id'];
            $loc1[$inc]['name'] = $dlrl_data['l1_name'];
            $inc++;
        }
        $dealer_id_fetch = empty($row['dealer_id_fetch']) ? "0" : $row['dealer_id_fetch'];
        $dealer_id_delete = empty($row['dealer_id_delete']) ? "0" : $row['dealer_id_delete'];
        $retailer_id_fetch = empty($row['retailer_id_fetch']) ? "0" : $row['retailer_id_fetch'];
        $retailer_id_delete = empty($row['retailer_id_delete']) ? "0" : $row['retailer_id_delete'];
        $location_id_fetch = empty($row['location_id_fetch']) ? "0" : $row['location_id_fetch'];
        $location_id_delete = empty($row['location_id_delete']) ? "0" : $row['location_id_delete'];
        $beat_id_fetch = empty($row['beat_id_fetch']) ? "0" : $row['beat_id_fetch'];
        $beat_id_delete = empty($row['beat_id_delete']) ? "0" : $row['beat_id_delete'];
        $product_id_fetch = empty($row['product_id_fetch']) ? "0" : $row['product_id_fetch'];
        $product_id_delete = empty($row['product_id_delete']) ? "0" : $row['product_id_delete'];
        $catalog_id_fetch = empty($row['catalog_id_fetch']) ? "0" : $row['catalog_id_fetch'];
        $catalog_id_delete = empty($row['catalog_id_delete']) ? "0" : $row['catalog_id_delete'];
//        $final_dealer_details1 = array();

//here we get user person details
        $query_person = "SELECT p.id AS rId, CONCAT_WS(' ',first_name,middle_name,last_name) AS full_name,mobile,address,dob FROM person p INNER JOIN person_details pd ON pd.person_id = p.id WHERE p.id = $row[id]";
        $run_person = mysqli_query($dbc, $query_person);
        $fetch_person = mysqli_fetch_assoc($run_person);
        $id = $fetch_person['rId'];
        $person_info[] = $fetch_person;
        $town = [];
        $dist = [];
// user person details end here
        if ($row['sync_status'] == '0') { // here we get all data
//This query is used to fetch dealer information
            if ($role_id != 5) {
                $query_dealer = "SELECT d.id,d.name,csa_id,lv.l4_name,lv.l4_id FROM dealer AS d INNER JOIN user_dealer_retailer 
AS udr ON udr.dealer_id = d.id INNER JOIN dealer_location_rate_list as dlrl ON dlrl.dealer_id=d.id INNER JOIN location_view as lv ON lv.l5_id=dlrl.location_id AND udr.user_id = '$row[id]' GROUP BY id DESC"; //user_id

                $run_dealer = mysqli_query($dbc, $query_dealer);
                if (mysqli_num_rows($run_dealer) > 0) {
                    $inc = 0;
                    while ($dealer_fetch = mysqli_fetch_assoc($run_dealer)) {
                        $dealer_info['id'] = $dealer_fetch['id'];
                        $dealer_info['name'] = $dealer_fetch['name'];
                        $dealer_info['lid'] = $dealer_fetch['l4_id'];
                        $dealer_info['lname'] = $dealer_fetch['l4_name'];
                        $dealer_array[] = $dealer_fetch['id'];
                        $final_dealer_details[] = $dealer_info;

                        $ddd[]=$dealer_fetch['id'];

                        $d_arr[]=$dealer_fetch['id'];

                    }
                }
                #User - Town
                if (!empty($d_arr)) {
                    $d_string=count($d_arr)>1?implode(',',$d_arr):$d_arr[0];
                    $myq = "SELECT l3_name,l3_id,l4_name,l4_id from dealer_location_rate_list LEFT JOIN location_view ON dealer_location_rate_list.location_id=location_view.l5_id where dealer_id IN ($d_string) GROUP BY l4_id";
//                    echo $myq;
//                    die;
                    $myq_data = mysqli_query($dbc, $myq);
                    if (mysqli_num_rows($myq_data) > 0) {

                        while ($myq_arr = mysqli_fetch_assoc($myq_data)) {
                            $town[$inc]['id'] = $myq_arr['l4_id'];
                            $town[$inc]['name'] = $myq_arr['l4_name'];
                            $dist[$inc]['id'] = $myq_arr['l3_id'];
                            $dist[$inc]['name'] = $myq_arr['l3_name'];

                            $inc++;
                        }
                    }
                }
            }

            if ($role_id == 5) {
///////////////////////PRODUCT FOR DEALER/////////////////////////////////////////////
// $query_catalog_product = "SELECT catalog_product.weight as weight, catalog_product.id,catalog_product.hsn_code,catalog_1.id as classification_id,catalog_1.name as classification_name,catalog_id,unit,catalog_product.name,cprl.base_price,cprl.rate as mrp,catalog_2.name as cname FROM catalog_product "dealer_pcs_rate
// . " INNER JOIN product_rate_list cprl ON catalog_product.id = cprl.product_id"
// . " INNER JOIN catalog_2 ON catalog_2.id=catalog_product.catalog_id INNER JOIN catalog_1 ON catalog_1.id=catalog_2.catalog_1_id WHERE stateId='$state_id' ORDER BY name ASC";


                $query_catalog_product = "SELECT catalog_product.id,catalog_product.weight as weight,catalog_1.id as classification_id,catalog_1.name as classification_name,catalog_id,unit,catalog_product.name,cprl.retailer_pcs_rate as base_price,cprl.mrp_pcs as mrp, cprl.mrp_pcs,hsn_code,catalog_2.name as cname, cprl.dealer_rate,cprl.dealer_pcs_rate FROM catalog_product "
                    . " INNER JOIN product_rate_list cprl ON catalog_product.id = cprl.product_id"
                    . " INNER JOIN catalog_2 ON catalog_2.id=catalog_product.catalog_id INNER JOIN catalog_1 ON catalog_1.id=catalog_2.catalog_1_id WHERE state_id='$state_id' AND catalog_product.division IN ($product_division) ORDER BY catalog_product.id ASC";
//h1($query_catalogproduct);


                $run_catalog_product = mysqli_query($dbc, $query_catalog_product);
//echo $query_catalog_product;
                $target_qty = '';
                if (mysqli_num_rows($run_catalog_product) > 0) {
                    while ($catalog_product_fetch = mysqli_fetch_assoc($run_catalog_product)) {

                        $i = '0';

                        $pid = $catalog_product_fetch['id'];
                        $hsn = $catalog_product_fetch['hsn_code'];
                        $focus = mysqli_query($dbc, "select `product_id` from `focus` where `product_id`=$pid");
                        if (mysqli_num_rows($focus) > 0) {
                            $i = '1';
                        }

//*********************  FOCUS TARGET ADDED 04-12-2017 ***********************
                        $now_date = date('Y-m-d');
                        $focust = mysqli_query($dbc, "SELECT `target_qty` FROM `focus_product_users_target` WHERE `product_id`=$pid AND user_id=$id AND '$now_date' BETWEEN `start_date` AND `end_date`");

                        if (mysqli_num_rows($focust) > 0) {
                            $ftarget = mysqli_fetch_assoc($focust);
                            $target_qty = $ftarget['target_qty'];
                        }
//////////////////////////////////TAX////////ANK////////////////////////////////
// $taxx = mysqli_query($dbc,"SELECT tax FROM `catalog_product_rate_list` where `catalog_product_id`=$pid AND `stateId`=$state_id");
// $row_tax = mysqli_fetch_assoc($taxx);

//////////////////////////////////TAX////////ANK////////////////////////////////
                        $querytax = "SELECT igst as tax FROM `_gst` where `hsn_code`='$hsn'";
//h1($querytax);
                        $taxx = mysqli_query($dbc, $querytax);
                        $row_tax = mysqli_fetch_assoc($taxx);

/////////////////////////////////////////////////////////////////////////////////////////////////////////
                        $catalog_product_info['id'] = $catalog_product_fetch['id'];
                        $catalog_product_info['classification_id'] = $catalog_product_fetch['classification_id'];
                        $catalog_product_info['classification_name'] = $catalog_product_fetch['classification_name'];
                        $catalog_product_info['category'] = $catalog_product_fetch['catalog_id'];
                        $catalog_product_info['hsn_code'] = $catalog_product_fetch['hsn_code'];
                        $catalog_product_info['category_name'] = $catalog_product_fetch['cname'];
                        $catalog_product_info['name'] = $catalog_product_fetch['name'];
                        $catalog_product_info['weight'] = $catalog_product_fetch['weight'];
                        $catalog_product_info['base_price'] = $catalog_product_fetch['base_price'];
                        $catalog_product_info['dealer_rate'] = $catalog_product_fetch['dealer_rate'];
                        $catalog_product_info['dealer_pcs_rate'] = $catalog_product_fetch['dealer_pcs_rate'];
                        $catalog_product_info['mrp'] = $catalog_product_fetch['mrp'];
                        $catalog_product_info['pcs_mrp'] = $catalog_product_fetch['mrp_pcs'];
                        $catalog_product_info['unit'] = $catalog_product_fetch['unit'];
                        $catalog_product_info['focus'] = $i;
                        $catalog_product_info['focus_target'] = $target_qty;
                        $catalog_product_info['tax'] = !empty($row_tax['tax'])?$row_tax['tax']:'';
//$catalog_product_info['state'] = $state_id;
                        $final_catalog_product_details[] = $catalog_product_info;

                    }
                }
                $final_person_details1 = array();
                $query_dealer = "SELECT d.id,d.name,csa_id FROM dealer AS d INNER JOIN user_dealer_retailer AS udr ON udr.dealer_id = d.id AND udr.user_id = '$row[id]' GROUP BY id DESC"; //user_id

                $run_dealer = mysqli_query($dbc, $query_dealer);
                if (mysqli_num_rows($run_dealer) > 0) {
                    while ($dealer_fetch = mysqli_fetch_assoc($run_dealer)) {
                        $dealer_info1['id'] = $dealer_fetch['id'];
                        $dealer_info1['name'] = $dealer_fetch['name'];
                        $dealer_info1['ss_id'] = $dealer_fetch['csa_id'];
                        $cid = $dealer_fetch['csa_id'];
                        $ss = mysqli_query($dbc, "SELECT csa_name FROM `csa` WHERE c_id=$cid");
                        $ss_row = mysqli_fetch_assoc($ss);
                        $dealer_info1['ss_name'] = $ss_row['csa_name'];
                        $dealer_array[] = $dealer_fetch['id'];
                        $final_dealer_details1[] = $dealer_info1;
                    }
                }
/////////////////////////////THRESHOLD//////////////////////////////
                $threshold = array();
                $query_thres = "SELECT product_id, qty FROM `threshold` WHERE dealer_id='$dealer_info1[id]'"; //user_id
//echo $query_thres;
                $run_thres = mysqli_query($dbc, $query_thres);
                if (mysqli_num_rows($run_thres) > 0) {
                    while ($thres_fetch = mysqli_fetch_assoc($run_thres)) {
                        $threshold1['product_id'] = $thres_fetch['product_id'];
                        $threshold1['qty'] = $thres_fetch['qty'];

                        $threshold[] = $threshold1;
                    }
                }
/////////////////////NUMBER OF SALE////////////////////////////////
                $query_cs = "SELECT id FROM `user_sales_order` WHERE dealer_id='$dealer_info1[id]'";
                $run_cs = mysqli_query($dbc, $query_cs) or die(mysqli_error($dbc));
                $sale = mysqli_num_rows($run_cs);
                $query_ch = "SELECT id FROM `challan_order` WHERE ch_dealer_id='$dealer_info1[id]'";
                $run_ch = mysqli_query($dbc, $query_ch) or die(mysqli_error($dbc));
                $chal = mysqli_num_rows($run_ch);
// echo $dealer_info1['id']." ".$sale." ".$chal;
/////////////////////////////////////////////////////////////DEALER RETAILSER//////////////////////////////////////////////

///////////////////////////////////////////////////////////////USER FOR DISTRIBUTOR/////////////////////////////////////////
                $query_person = "SELECT p.id as id,CONCAT_WS(' ',first_name,middle_name,last_name) as name FROM person AS p INNER JOIN user_dealer_retailer AS udr ON udr.user_id = p.id AND udr.dealer_id = '$dealer_info1[id]' AND udr.user_id !='$row[id]' GROUP BY id DESC"; //user_id
//echo $query_person;
                $run_person = mysqli_query($dbc, $query_person);
                if (mysqli_num_rows($run_person) > 0) {
                    while ($person_fetch = mysqli_fetch_assoc($run_person)) {
                        $person_info1['id'] = $person_fetch['id'];
                        $person_info1['name'] = $person_fetch['name'];

                        $final_person_details1[] = $person_info1;
                    }
                }

/////////////////////GRAPH Target VALUE////////////////////////////////
                $dealer_target = array();
                $m = date('F');
// h1($m);
                $query_tar = "SELECT $m as month FROM `dealer_target` WHERE dealer_id='$dealer_info1[id]'";
//h1($query_tar);
                $run_tar = mysqli_query($dbc, $query_tar) or die(mysqli_error($dbc));
                if (mysqli_num_rows($run_tar) > 0) {
                    while ($sc_tar = mysqli_fetch_assoc($run_tar)) {
                        $tar_info['target'] = $sc_tar['month'];
                        $dealer_target[] = $tar_info;
                    }
                }
/////////////////////DEALER TARGET VALUE////////////////////////////////
                $dealer_targetgraph = array();
                $m = date('F');
                $datemonth = date("Y-m");
// h1($m);
                $query_cur = "SELECT $m as month FROM `dealer_target` WHERE dealer_id='$dealer_info1[id]'";
//h1($query_tar);
                $run_cur = mysqli_query($dbc, $query_cur) or die(mysqli_error($dbc));
                if (mysqli_num_rows($run_cur) > 0) {
                    $sc_cur = mysqli_fetch_assoc($run_cur);
                    $cur_info['target'] = $sc_cur['month'];
                    $ach_cur = "SELECT SUM(amount) as amount FROM `challan_order` WHERE `ch_dealer_id` = '$dealer_info1[id]' AND
    DATE_FORMAT(ch_date,'%Y-%m')='$datemonth'";
                    //echo $qach;
                    $mach_cur = mysqli_query($dbc, $ach_cur);
                    $currows = mysqli_fetch_assoc($mach_cur);
                    $cur_info['achieved'] = $currows['amount'];

////////////////////////////DEALER TARGET PREV///////////////////////
                    $prev_month = date('F', strtotime('last month'));
                    $prevm = strtolower($prev_month);
                    $last_month = date('Y-m', strtotime('last month'));
                    $query_prev = "SELECT $prevm as prevmonth FROM `dealer_target` WHERE dealer_id='$dealer_info1[id]'";
//h1($query_tar);
                    $run_prev = mysqli_query($dbc, $query_prev) or die(mysqli_error($dbc));
                    $sc_prev = mysqli_fetch_assoc($run_prev);
                    $cur_info['prev'] = $sc_prev['prevmonth'];
                    $ach_prev = "SELECT SUM(amount) as preamount FROM `challan_order` WHERE `ch_dealer_id` = '$dealer_info1[id]' AND
    DATE_FORMAT(ch_date,'%Y-%m')='$last_month'";
//echo $ach_prev; exit;
                    $mach_prev = mysqli_query($dbc, $ach_prev);
                    $prerows = mysqli_fetch_assoc($mach_prev);
                    $cur_info['prev_achieved'] = $prerows['preamount'];

////////////////////////////DEALER TARGET next///////////////////////
                    $nextmonth = date('F', strtotime('+1 month'));
                    $nextm = strtolower($nextmonth);
                    $datenext = date('Y-m', strtotime('+1 month'));
                    $query_next = "SELECT $nextm as nextmonth FROM `dealer_target` WHERE dealer_id='$dealer_info1[id]'";
//h1($query_tar);
                    $run_next = mysqli_query($dbc, $query_next) or die(mysqli_error($dbc));
                    $sc_next = mysqli_fetch_assoc($run_next);
                    $cur_info['next'] = $sc_next['nextmonth'];


                    $dealer_targetgraph[] = $cur_info;

                }
///////////////////////////////////////////////////////////////PRODUCT CAT SALE/////////////////////////////////////////
                $monthsale = date("Y-m");
                $catalogsale = array();
                $query_sale = "SELECT SUM(quantity) qty,SUM(rate*quantity) value, c1_name,c1_id FROM `user_sales_order_details` usod INNER JOIN `user_sales_order` uso ON usod.order_id = uso.order_id INNER JOIN catalog_view ON catalog_view.product_id = usod.product_id WHERE DATE_FORMAT(date,'%Y-%m') = '$monthsale' AND dealer_id='$dealer_info1[id]' group by catalog_view.c1_id"; //user_id
//echo $query_sale;
                $run_sale = mysqli_query($dbc, $query_sale);
                while ($sale_fetch = mysqli_fetch_assoc($run_sale)) {
                    $sale_info1['name'] = $sale_fetch['c1_name'];
                    $sale_info1['id'] = $sale_fetch['c1_id'];
                    $sale_info1['qty'] = $sale_fetch['qty'];
                    $sale_info1['value'] = $sale_fetch['value'];

                    $catalogsale[] = $sale_info1;

                }
///////////////////////////////////////////////////////////////PREV PRODUCT CAT SALE/////////////////////////////////////////
                $last_sale = date('Y-m', strtotime('last month'));
                $lcatalogsale = array();
                $query_lsale = "SELECT SUM(quantity) qty,SUM(rate*quantity) value, c1_name,c1_id FROM `user_sales_order_details` usod INNER JOIN `user_sales_order` uso ON usod.order_id = uso.order_id INNER JOIN catalog_view ON catalog_view.product_id = usod.product_id WHERE DATE_FORMAT(date,'%Y-%m') = '$last_sale' AND dealer_id='$dealer_info1[id]' group by catalog_view.c1_id"; //user_id
//echo $query_sale;
                $run_lsale = mysqli_query($dbc, $query_lsale);
                while ($lsale_fetch = mysqli_fetch_assoc($run_lsale)) {
                    $lsale_info1['name'] = $lsale_fetch['c1_name'];
                    $lsale_info1['id'] = $lsale_fetch['c1_id'];
                    $lsale_info1['qty'] = $lsale_fetch['qty'];
                    $lsale_info1['value'] = $lsale_fetch['value'];

                    $lcatalogsale[] = $lsale_info1;

                }

////////////////////////////????ACHIEVED///////////////////////////////////

/////////////////////VAN DETAILS////////////////////////////////
                $van = array();
                $query_van = "SELECT * FROM  `van` WHERE dealer_id='$dealer_info1[id]'";
                //h1($query_tar);
                $run_van = mysqli_query($dbc, $query_van) or die(mysqli_error($dbc));
                if (mysqli_num_rows($run_tar) > 0) {
                    while ($sc_van = mysqli_fetch_assoc($run_van)) {
                        $van_info['van_id'] = $sc_van['vanId'];
                        $van_info['van_no'] = $sc_van['van_no'];
                        $van_info['mobile'] = $sc_van['mobile'];
                        $van_info['license_no'] = $sc_van['license_no'];
                        $van_info['driver_name'] = $sc_van['driver_name'];
                        $van_info['capacity'] = $sc_van['capacity'];
                        $van[] = $van_info;
                    }
                }
//////////////////////////////////////////////////////

////////////////////////////RETAILER DEALER///////////////////////////////////
// $ch = "SELECT SUM(taxable_amt) as achived,date_format(`ch_date`,'%Y-%m-%d') as ch_date FROM `challan_order` as co INNER JOIN challan_order_details as cod ON cod.ch_id=co.id WHERE ch_dealer_id='$dealer_info1[id]' AND date_format(`ch_date`,'%m')='$m' GROUP BY ch_date ASC";
//h1($ch);
// $chre = "SELECT r.id as id,r.email as email1,r.landline as contact,r.contact_per_name as contact_person,r.other_numbers,r.name as name,
// location_id,rl.name AS loc_name
// ,r.address as address,r.tin_no as tin,lat_long FROM retailer r INNER JOIN user_dealer_retailer udr ON udr.retailer_id = r.id
// INNER JOIN location_$dconstant_data[retailer_level] AS rl ON rl.id = r.location_id WHERE r.dealer_id='$dealer_info1[id]' GROUP BY r.id";

                $chre = "SELECT r.id as id,r.email as email1,r.landline as contact,r.contact_per_name as contact_person,r.other_numbers,r.name as name,
location_id,rl.l5_name AS loc_name
,rl.state_id,r.address as address,r.tin_no as tin,lat_long FROM retailer r INNER JOIN user_dealer_retailer udr ON udr.retailer_id = r.id
INNER JOIN location_view AS rl ON rl.l5_id = r.location_id WHERE r.dealer_id='$dealer_info1[id]' GROUP BY r.id";
//h1($chre);die;
                $run_chre = mysqli_query($dbc, $chre);
                if (mysqli_num_rows($run_chre) > 0) {
                    $run_chre_info = array();
                    $run_chre_dealer = array();
                    while ($sc_ch = mysqli_fetch_assoc($run_chre)) {
                        $ll = $sc_ch['lat_long'];
                        $latlng = explode(",", $ll);
                        $run_chre_dealer['id'] = $sc_ch['id'];
                        $run_chre_dealer['lat'] = $latlng[0];
                        $run_chre_dealer['lng'] = $latlng[1];
                        $run_chre_dealer['name'] = $sc_ch['name'];
                        $run_chre_dealer['location_id'] = $sc_ch['location_id'];
                        $run_chre_dealer['retailer_state_id'] = $sc_ch['state_id'];
                        $run_chre_dealer['loc_name'] = $sc_ch['loc_name'];
                        $run_chre_dealer['address'] = $sc_ch['address'];
                        $run_chre_dealer['email'] = $sc_ch['email1'];
                        $run_chre_dealer['contact_no'] = $sc_ch['contact'];
                        $run_chre_dealer['contact_person'] = $sc_ch['contact_person'];
                        $run_chre_dealer['tin'] = $sc_ch['tin'];
                        $run_chre_info[] = $run_chre_dealer;
// print_r($run_chre_dealer);
                    }
                }

            } else {
//This query is Used to send catalog_product name list
//                echo $state_id;die;
                $query_catalogproduct = "SELECT catalog_product.id,catalog_1.id as classification_id,catalog_1.name as classification_name,catalog_id,unit,catalog_product.name,cprl.retailer_pcs_rate as base_price,cprl.mrp_pcs as mrp,cprl.mrp_pcs,hsn_code,catalog_2.name as cname, cprl.dealer_rate,cprl.dealer_pcs_rate FROM catalog_product "
                    . " INNER JOIN product_rate_list cprl ON catalog_product.id = cprl.product_id"
                    . " INNER JOIN catalog_2 ON catalog_2.id=catalog_product.catalog_id INNER JOIN catalog_1 ON catalog_1.id=catalog_2.catalog_1_id WHERE state_id='$state_id' AND catalog_product.division IN ($product_division) ORDER BY catalog_product.id ASC";

                $run_catalogproduct = mysqli_query($dbc, $query_catalogproduct) or die(mysqli_error($dbc));
//echo "ANKUSH2";
//echo $run_catalogproduct;
//echo $run_count = count($run_catalogproduct);
                if (mysqli_num_rows($run_catalogproduct) > 0) {
//echo "ANKUSH3";
                    $final_catalog_product_details = array();
                    $catalog_productinfo = array();
                    $target_qty = '';
                    while ($catalog_productfetch = mysqli_fetch_assoc($run_catalogproduct)) {
//echo "ANKUSH4";

                        $pid = $catalog_productfetch['id'];
                        $hsn = $catalog_productfetch['hsn_code'];
                        $fq = "select `product_id` from `focus` where `product_id`=$pid";
                        $focus = mysqli_query($dbc, $fq);
                        if (mysqli_num_rows($focus) > 0) {
                            $i = '1';
                        } else {
                            $i = '0';
                        }
//////////////////////////////////TAX////////ANK////////////////////////////////
                        $querytax = "SELECT igst as tax FROM `_gst` where `hsn_code`='$hsn'";
//h1($querytax);
                        $taxx = mysqli_query($dbc, $querytax);
                        $row_tax = mysqli_fetch_assoc($taxx);

//*********************  FOCUS TARGET ADDED 04-12-2017 ***********************
                        $now_date = date('Y-m-d');
                        $focust = mysqli_query($dbc, "SELECT `target_value` FROM `focus_product_users_target` WHERE `product_id`=$pid AND user_id=$id AND '$now_date' BETWEEN `start_date` AND `end_date`");
//h1("SELECT `target_qty` FROM `focus_product_users_target` WHERE `product_id`=$pid AND user_id=$id AND '$now_date' BETWEEN `start_date` AND `end_date`");

                        if (mysqli_num_rows($focust) > 0) {
                            $ftarget = mysqli_fetch_assoc($focust);
                            $target_qty = $ftarget['target_value'];
                        } else {
                            $target_qty = '0';
                        }

/////////////////////////////////////////////////////////////////////////////////////////////////////////
                        $catalog_productinfo['id'] = $catalog_productfetch['id'];
                        $catalog_productinfo['classification_id'] = $catalog_productfetch['classification_id'];
                        $catalog_productinfo['classification_name'] = $catalog_productfetch['classification_name'];
                        $catalog_productinfo['category'] = $catalog_productfetch['catalog_id'];
                        $catalog_productinfo['hsn_code'] = $catalog_productfetch['hsn_code'];
                        $catalog_productinfo['category_name'] = $catalog_productfetch['cname'];
                        $catalog_productinfo['name'] = $catalog_productfetch['name'];
                        $catalog_productinfo['base_price'] = $catalog_productfetch['base_price'];
                        $catalog_productinfo['dealer_rate'] = $catalog_productfetch['dealer_rate'];
                        $catalog_productinfo['dealer_pcs_rate'] = $catalog_productfetch['dealer_pcs_rate'];
                        $catalog_productinfo['mrp'] = $catalog_productfetch['mrp'];
                        $catalog_productinfo['pcs_mrp'] = $catalog_productfetch['mrp_pcs'];
                        $catalog_productinfo['unit'] = $catalog_productfetch['unit'];
                        $catalog_productinfo['focus'] = $i;
                        $catalog_productinfo['focus_target'] = $target_qty;
                        $catalog_productinfo['tax'] = !empty($row_tax['tax'])?$row_tax['tax']:'';
//$catalog_product_info['state'] = $state_id;
                        $final_catalog_product_details[] = $catalog_productinfo;

                    }
                }
            }
            $dealerarray = 0;
            if (!empty($dealer_array)) {
//This dealer information end here
                $dealerarray = implode(",", $dealer_array); // here we get all the dealer id
//This query is used to fetch location of the dealer in other words we can say that here we find out beat

                //Beat->locname
                $query_locality = "SELECT l.name AS locname,l.id AS lid,dealer_id "
                    . "FROM dealer_location_rate_list AS dl INNER "
                    . "JOIN location_$dconstant_data[dealer_level] AS l "
                    . "ON l.id = dl.location_id "
                    . "WHERE dl.user_id=$id AND dl.dealer_id in ($dealerarray) group by dealer_id,lid"; //here we get beat
//echo $query_locality; exit();
                $run_locality = mysqli_query($dbc, $query_locality) or die(mysqli_error($dbc));
                if (mysqli_num_rows($run_locality) > 0) {
                    while ($locality_fetch = mysqli_fetch_assoc($run_locality)) {
                        $beat_info['id'] = $locality_fetch['lid'];
                        $beat_array[] = $locality_fetch['lid'];
                        $beat_info['name'] = $locality_fetch['locname'];
                        $beat_info['dealer_id'] = $locality_fetch['dealer_id'];
                        $final_beat_details[] = $beat_info;
                        $final_dealer_location_details[] = $beat_info;


//                        $l4 = "SELECT location_4.name as lname, location_4.id as lid" .
//                            " from location_5 left JOIN  location_4 ON location_4.id = location_5.location_4_id"
//                            . " where location_5.id= $locality_fetch[lid]";
//                        if (mysqli_num_rows($l4) > 0) {
//                            while ($lf1 = mysqli_fetch_assoc($l4)) {
//                                $temp1['name'] = $lf1['lname'];
//                                $temp1['id'] = $lf1['lid'];
//                                $town[] = $temp1;
//
//                                $l3 = "SELECT location_3.name as lname, location_3.id as lid" .
//                                    " from location_4 left JOIN  location_3 ON location_4.id = location_4.location_3_id"
//                                    . " where location_4.id= $lf1[lid]";
//                                if (mysqli_num_rows($l3) > 0) {
//                                    while ($lf2 = mysqli_fetch_assoc($l3)) {
//                                        $temp2['name'] = $lf2['lname'];
//                                        $temp2['id'] = $lf2['lid'];
//                                        $dist[] = $temp2;
//                                    }
//                                }
//                            }
//                        }
                    }
                }
            }

            if (!empty($beat_array)) {
//This query is used to fetch location of the dealer in other words we can say that here we find out beat end here //
                $reatiler_loc_array = array();
                $locationarray = implode(",", $beat_array);

                $dlevel = $dconstant_data['dealer_level'];
                $rlevel = $dconstant_data['retailer_level'];
                $querylocation = "SELECT dealer_id,location_$rlevel.id AS location,location_$rlevel.name FROM location_$dlevel";
                for ($i = $dlevel; $i < $rlevel; $i++) {
                    $j = $i + 1;
                    $querylocation .= " INNER JOIN location_$j ON location_$j.location_" . $i . "_id = location_$i.id ";
                }
                $querylocation .= " INNER JOIN dealer_location_rate_list ON location_$dlevel.id = dealer_location_rate_list.location_id WHERE location_$dlevel.id IN ($locationarray) AND dealer_id IN ($dealerarray) GROUP BY location DESC";

//                echo $querylocation;die;

                $rqlocation = mysqli_query($dbc, $querylocation) or die(mysqli_error($dbc));
                if (mysqli_num_rows($rqlocation) > 0) {
                    while ($dlocation = mysqli_fetch_assoc($rqlocation)) {
                        $dealer_location['dealer_id'] = $dlocation['dealer_id'];
                        $dealer_location['id'] = $dlocation['location'];
                        $reatiler_loc_array[] = $dlocation['location'];
                        $dealer_location['name'] = $dlocation['name'];
// $final_dealer_location_details[] = $dealer_location;
                    }
                }
            } //if(!empty($beat_array)) end here
//here some confusion and i need to ask sujeet sir..
            if (!empty($reatiler_loc_array))
                $reatiler_loc_array_str = implode(',', $reatiler_loc_array);
            else
                $reatiler_loc_array_str = '';
            $query_retailer_access_on = "SELECT udr.seq_id,r.id,r.email as email1,r.landline,r.contact_per_name,r.other_numbers,r.name,location_id,rl.name AS loc_name,r.address,r.tin_no as tin,lat_long FROM retailer r INNER JOIN user_dealer_retailer udr ON udr.retailer_id = r.id INNER JOIN location_$dconstant_data[retailer_level] AS rl ON rl.id = r.location_id WHERE user_id = '$row[id]' AND r.location_id IN ($reatiler_loc_array_str) AND retailer_status='1' GROUP BY id DESC";

            $run_retailer = mysqli_query($dbc, $query_retailer_access_on);
            if (mysqli_num_rows($run_retailer) > 0) {
                while ($retailer_fetch = mysqli_fetch_assoc($run_retailer)) {
                    $retailer_info['id'] = $retailer_fetch['id'];
                    $lat_lng = $retailer_fetch['lat_long'];

                    $rso = mysqli_query($dbc, "SELECT sum(total_amount) as paid FROM payment_collection WHERE retailer_id = '$retailer_info[id]'");
                    $row_rso = mysqli_fetch_assoc($rso);
                    $paid = $row_rso['paid'];
//$date_pay = $row_rso['date'];

                    $ccso = mysqli_query($dbc, "SELECT SUM(`amount`) AS ch_amt FROM `challan_order` WHERE ch_retailer_id = '$retailer_info[id]'");

                    $row_cso = mysqli_fetch_assoc($ccso);
                    $ch_amt = $row_cso['ch_amt'];
                    $outstanding = $ch_amt - $paid;

                    $rso1 = mysqli_query($dbc, "SELECT total_amount FROM payment_collection WHERE retailer_id = '$retailer_info[id]' Order By pay_date_time limit 0,1");
                    $row_rso1 = mysqli_fetch_assoc($rso1);
                    $last = $row_rso1['total_amount'];

                    $ll = explode(",", $lat_lng);
                    $lat = $ll[0];
                    $lng = $ll[1];
                    $retailer_info['lat'] = $lat;
                    $retailer_info['lng'] = $lng;
                    $retailer_info['seq_no'] = $retailer_fetch['seq_id'];
                    $retailer_info['name'] = $retailer_fetch['name'];
                    $retailer_info['location_id'] = $retailer_fetch['location_id'];
                    $retailer_info['loc_name'] = $retailer_fetch['loc_name'];
                    $retailer_info['address'] = $retailer_fetch['address'];
                    $retailer_info['email'] = $retailer_fetch['email1'];
                    $retailer_info['achieved'] = $ch_amt;
                    $retailer_info['outstanding'] = $outstanding;
                    if (!empty($last))
                        $retailer_info['last_amt'] = $last;
                    else
                        $retailer_info['last_amt'] = 0;
//if(!empty($date_pay))
/// $retailer_info['last_date'] = $date_pay;
//else
                    $retailer_info['last_date'] = "No Date";

                    $retailer_info['contact_no'] = $retailer_fetch['landline'] . ',' . $retailer_fetch['other_numbers'];
                    $retailer_info['contact_person'] = $retailer_fetch['contact_per_name'];
                    $retailer_info['tin'] = $retailer_fetch['tin'];
                    $final_retailer_details[] = $retailer_info;
                }
            }

//This query is Used to send catalog Classification name list
            $final_product_classification_details = array();
            $query_classification = "SELECT catalog_1.id as id, catalog_1.name as name FROM `catalog_1` INNER JOIN `catalog_view` ON catalog_1.id = c1_id WHERE division IN ($product_division) GROUP BY c1_id";
            $run_classifiction = mysqli_query($dbc, $query_classification);
            if (mysqli_num_rows($run_classifiction) > 0) {
                while ($classification_fetch = mysqli_fetch_object($run_classifiction)) {
                    $classification_info['id'] = $classification_fetch->id;
                    $classification_info['name'] = $classification_fetch->name;
                    $final_product_classification_details[] = $classification_info;
                }

            }

            // if(mysqli_num_rows($run_catalog_product)if(mysqli_num_rows($run_catalog_product)


            $query_damage_product = "SELECT id,`name` as catalog_product,base_price as rate,'1' as state_id FROM catalog_product";

            $run_damage_product = mysqli_query($dbc, $query_damage_product) or die(mysqli_error($dbc));

            while ($damage_product_fetch = mysqli_fetch_assoc($run_damage_product)) {
                $damage_product_info['id'] = $damage_product_fetch['id'];
                $damage_product_info['catalog_product'] = $damage_product_fetch['catalog_product'];

                $damage_product_info['rate'] = $damage_product_fetch['rate'];
                $final_damage_product_details[] = $damage_product_info;
            }
// h1($query_damage_product);

            $comp = array();
            $final_comp_details = array();
            $compa = array();
            $final_compa_details = array();
            $cdata = "SELECT id, name FROM complaint_type";
//h1($cdata);
            $cquery = mysqli_query($dbc, $cdata);
            if ($cquery) {
                while ($complain = mysqli_fetch_assoc($cquery)) {
                    $comp['id'] = $complain['id'];
                    $comp['name'] = $complain['name'];
                    $final_comp_details[] = $comp;
                }
            }

//final_isr_array
            $idealer_data = "SELECT GROUP_CONCAT(DISTINCT dealer_id ORDER BY dealer_id ASC SEPARATOR ',') as dealer_id FROM user_dealer_retailer where user_id='$person_id' ";
// h1($idealer_data);
            $idlr_query = mysqli_query($dbc, $idealer_data);
            $idlr_row = mysqli_fetch_object($idlr_query);
            $idealer_id = $idlr_row->dealer_id;

// $isr_data = "SELECT rolename,person.id,CONCAT_WS(' ',first_name,middle_name,last_name) as name,dealer_id,dealer.name as dealer_name FROM person INNER JOIN _role ON _role.role_id=person.role_id INNER JOIN user_dealer_retailer udr ON udr.user_id=person.id INNER JOIN dealer ON udr.dealer_id=dealer.id where dealer_id IN($idealer_id) and (person.role_id='14' OR person.role_id='15') group by dealer_id,person.id";

            $isr_data = "SELECT rolename,person.id,CONCAT_WS(' ',first_name,middle_name,last_name) as name,dealer_id,dealer.name as dealer_name FROM person INNER JOIN _role ON _role.role_id=person.role_id
   INNER JOIN dealer_location_rate_list udr ON udr.user_id=person.id
   INNER JOIN dealer ON udr.dealer_id=dealer.id where person_id_senior =$person_id and (person.role_id='14' OR person.role_id='15') group by dealer_id,person.id";
// h1($isr_data);
            $isr_query = mysqli_query($dbc, $isr_data);
            if ($isr_query) {
                while ($isr = mysqli_fetch_assoc($isr_query)) {
                    $isr_detail['id'] = $isr['id'];
                    $isr_detail['isr_name'] = $isr['name'] . '/' . $isr['rolename'];
                    $isr_detail['isr_dealer_id'] = $isr['dealer_id'];
                    $isr_detail['isr_dealer_name'] = $isr['dealer_name'];
                    $isr_array[] = $isr_detail;
                }
//pre($final_isr_array);die;
            }


//user_for_manual_attendance
            $user_for_manual_attendance = $myobj->recursiveall2_signin($row[id]);
            $user_for_manual_attendance = isset($_SESSION['juniordata']) ? $_SESSION['juniordata'] : array();


            $final_isr_array = (array_merge($isr_array, $user_for_manual_attendance));
            $final_isr_array = isset($final_isr_array) ? $final_isr_array : array();

            $cdataa = "SELECT id, name FROM user_category";
            $cquerya = mysqli_query($dbc, $cdataa);
            if ($cquerya) {
                while ($complaina = mysqli_fetch_assoc($cquerya)) {
                    $compa['id'] = $complaina['id'];
                    $compa['name'] = $complaina['name'];
                    $final_compa_details[] = $compa;
                }
            }
//This query is used to send top level category list
            $query_category = "SELECT id,name, catalog_view.c1_id as classification_id, catalog_view.c1_name as classification_name FROM catalog_2 INNER JOIN `catalog_view` ON catalog_2.id = c2_id WHERE division IN ($product_division) GROUP BY c2_id ORDER BY id ASC";
            $run_category = mysqli_query($dbc, $query_category) or die(mysqli_error($dbc));
            if (mysqli_num_rows($run_category) > 0) {
                while ($category_fetch = mysqli_fetch_assoc($run_category)) {
                    $category_info['id'] = $category_fetch['id'];
                    $category_info['classification_id'] = $category_fetch['classification_id'];
                    $category_info['classification_name'] = $category_fetch['classification_name'];
                    $category_info['name'] = $category_fetch['name'];
                    $final_category_details[] = $category_info;
                }
            }

/////////////////////LEAVE////////////////////////////////
            $query_l = "SELECT id,name FROM `leave_type` ORDER BY id ASC";
            $run_l = mysqli_query($dbc, $query_l) or die(mysqli_error($dbc));
            if (mysqli_num_rows($run_l) > 0) {
                while ($l_fetch = mysqli_fetch_assoc($run_l)) {
                    $l_info['id'] = $l_fetch['id'];
                    $l_info['name'] = $l_fetch['name'];
                    $l_info['leave_value'] = 0;

                    $leave[] = $l_info;
                }
            }
//////////////////////////////////////////////////////////////
/////////////////////Stock////////////////////////////////
            $query_s = "SELECT product_id,rate,dealer_id,remaining FROM `stock` where dealer_id IN($dealerarray)";
            $run_s = mysqli_query($dbc, $query_s) or die(mysqli_error($dbc));
            if (mysqli_num_rows($run_s) > 0) {
                while ($s_fetch = mysqli_fetch_assoc($run_s)) {
                    $s_info['product_id'] = $s_fetch['product_id'];
// $s_info['rate'] = $s_fetch['rate'];
                    $s_info['qty'] = $s_fetch['remaining'];
                    $s_info['dealer_id'] = $s_fetch['dealer_id'];
                    $stock[] = $s_info;
                }
            }
////////////////////////////////////////////////////
/////////////////////GIFT////////////////////////////////
            $query_g = "SELECT * FROM `_retailer_mkt_gift` ORDER BY id ASC";
            $run_g = mysqli_query($dbc, $query_g) or die(mysqli_error($dbc));
            if (mysqli_num_rows($run_g) > 0) {
                while ($g_fetch = mysqli_fetch_assoc($run_g)) {
                    $g_info['id'] = $g_fetch['id'];
                    $g_info['name'] = $g_fetch['gift_name'];
                    $gift[] = $g_info;
                }
            }
////////////////////////////////////////////////////
/////////////////////TARGET ACHIEVED////////////////////////////////
            $query_ta = "SELECT svp.id as id,svp.value as value,svp.value_to as value_to,svp.scheme_gift as scheme_gift,svpd.start_date as start_date,svpd.end_date as end_date from scheme_value_product_details svp INNER JOIN scheme_value svpd ON svpd.scheme_id = svp.scheme_id where NOW() BETWEEN svpd.start_date AND svpd.end_date AND user = 2 AND state_id = $state_id";
            $run_ta = mysqli_query($dbc, $query_ta) or die(mysqli_error($dbc));
            if (mysqli_num_rows($run_ta) > 0) {
                while ($ta_fetch = mysqli_fetch_assoc($run_ta)) {
                    $ta_info['id'] = $ta_fetch['id'];
                    $ta_info['value'] = $ta_fetch['value'];
                    $ta_info['value_to'] = $ta_fetch['value_to'];
                    $ta_info['scheme_gift'] = $ta_fetch['scheme_gift'];
                    $ta_info['start_date'] = $ta_fetch['start_date'];
                    $ta_info['end_date'] = $ta_fetch['end_date'];
                    $q = "SELECT SUM(rate*(quantity+scheme_qty)) as achieved FROM `user_primary_sales_order` upso INNER JOIN user_primary_sales_order_details upsod ON upsod.order_id = upso.order_id where is_claim=0 AND action=1 AND ch_date >='$ta_info[start_date]' AND ch_date<='$ta_info[end_date]'";
// h1($q);
                    $r = mysqli_query($dbc, $q);
                    $row = mysqli_fetch_assoc($r);
                    $ta_info['achieved'] = $row['achieved'];
//echo $ta_info['achieved'];
                    $target_achieved[] = $ta_info;
                }
            }

////////////////////////////////////////////////////
/////////////////////Retailer TARGET////////////////////////////////
            $query_ta = "SELECT svp.id as id,svp.value as value,svp.value_to as value_to,svp.scheme_gift as scheme_gift,svpd.start_date as start_date,svpd.end_date as end_date from scheme_value_product_details svp INNER JOIN scheme_value svpd ON svpd.scheme_id = svp.scheme_id where NOW() BETWEEN svpd.start_date AND svpd.end_date AND user = 3 AND state_id = $state_id";
            $run_ta = mysqli_query($dbc, $query_ta) or die(mysqli_error($dbc));
            if (mysqli_num_rows($run_ta) > 0) {
                while ($ta_fetch = mysqli_fetch_assoc($run_ta)) {
                    $ta_info['id'] = $ta_fetch['id'];
                    $ta_info['value'] = $ta_fetch['value'];
                    $ta_info['value_to'] = $ta_fetch['value_to'];
                    $ta_info['scheme_gift'] = $ta_fetch['scheme_gift'];
                    $ta_info['start_date'] = $ta_fetch['start_date'];
                    $ta_info['end_date'] = $ta_fetch['end_date'];
// $q= "SELECT SUM(rate*(quantity+scheme_qty)) as achieved FROM `user_primary_sales_order` upso INNER JOIN user_primary_sales_order_details upsod ON upsod.order_id = upso.order_id where is_claim=0 AND action=1 AND ch_date >='$ta_info[start_date]' AND ch_date<='$ta_info[end_date]'";
// // h1($q);
// $r = mysqli_query($dbc,$q);
// $row = mysqli_fetch_assoc($r);
                    $ta_info['achieved'] = $ta_fetch['achieved'];
//echo $ta_info['achieved'];
                    $retailer_target[] = $ta_info;
                }
            }
////////////////////////////////////////////////////
/////////////////////Scheme////////////////////////////////
            $query_sc = "SELECT * FROM `scheme_product_details` WHERE scheme_quantity>=1 ORDER BY id ASC";
            $run_sc = mysqli_query($dbc, $query_sc) or die(mysqli_error($dbc));
            if (mysqli_num_rows($run_sc) > 0) {
                while ($sc_fetch = mysqli_fetch_assoc($run_sc)) {
                    $sc_info['id'] = $sc_fetch['id'];
                    $sc_info['product_id'] = $sc_fetch['product_id'];
                    $sc_info['buy_quantity'] = $sc_fetch['buy_quantity'];
                    $sc_info['scheme_quantity'] = $sc_fetch['scheme_quantity'];
                    $sc_info['start_date'] = $sc_fetch['start_date'];
                    $sc_info['end_date'] = $sc_fetch['end_date'];
                    $scheme[] = $sc_info;
                }
            }
/////////////////////Complaint////////////////////////////////
// $comp = "SELECT * FROM `complaint` where user_id = $id ORDER BY id ASC";
// $run_comp = mysqli_query($dbc, $comp) or die(mysqli_error($dbc));
// if (mysqli_num_rows($run_comp) > 0) {
/// while ($comp_fetch = mysqli_fetch_assoc($run_comp)) {
// $comp_info['complaint_id'] = $comp_fetch['complaint_id'];
// $ctype = $comp_fetch['complaint_type'];
// $cdata = "SELECT id, name FROM complaint_type where `id`=$ctype";
            $cdata = "SELECT id, name FROM complaint_type";
            $cquery = mysqli_query($dbc, $cdata);
            while ($complain = mysqli_fetch_assoc($cquery)) {
                $comp_info['complaint_id'] = $complain['id'];
                $comp_info['complaint_type'] = $complain['name'];
                $comp_info['action'] = '1';
                $complaint[] = $comp_info;
            }
// $comp_info['action'] = $comp_fetch['action'];

// }
// }
///////////////////////////////////////////////////////////////PREV PRODUCT CAT SALE/////////////////////////////////////////
            $last_sale = date('Y-m', strtotime('last month'));
            $lcatalogsale = array();
            $query_lsale = "SELECT SUM(quantity) qty,SUM(rate*quantity) value, c1_name,c1_id FROM `user_sales_order_details` usod INNER JOIN `user_sales_order` uso ON usod.order_id = uso.order_id INNER JOIN catalog_view ON catalog_view.product_id = usod.product_id WHERE DATE_FORMAT(date,'%Y-%m') = '$last_sale' AND dealer_id='$dealer_info1[id]' group by catalog_view.c1_id"; //user_id
//echo $query_sale;
            $run_lsale = mysqli_query($dbc, $query_lsale);
            while ($lsale_fetch = mysqli_fetch_assoc($run_lsale)) {
                $lsale_info1['name'] = $lsale_fetch['c1_name'];
                $lsale_info1['id'] = $lsale_fetch['c1_id'];
                $lsale_info1['qty'] = $lsale_fetch['qty'];
                $lsale_info1['value'] = $lsale_fetch['value'];

                $lcatalogsale[] = $lsale_info1;

            }

/////////////////////MTP////////////////////////////////
            $cdate = date('Y-m-d');
            $query_mtp = "SELECT rd,total_sales,working_date,locations,task_of_the_day FROM `monthly_tour_program` WHERE date_format(`working_date`,'%Y-%m')= date_format('$cdate','%Y-%m') AND person_id='$person_id' ORDER BY id ASC";
            $run_mtp = mysqli_query($dbc, $query_mtp) or die(mysqli_error($dbc));
            if (mysqli_num_rows($run_mtp) > 0) {
                while ($sc_mtp = mysqli_fetch_assoc($run_mtp)) {
                    $mtp_info['total_sale'] = $sc_mtp['total_sales'];
                    $mtp_info['rd'] = $sc_mtp['rd'];
                    $mtp_info['date'] = $sc_mtp['working_date'];
                    $loc_mt = $sc_mtp['locations'];
                    $loc = mysqli_query($dbc, "SELECT name FROM `location_5` WHERE `id` = $loc_mt");
                    $loc_row = mysqli_fetch_assoc($loc);
                    $mtp_info['today'] = $loc_row['name'];
                    $mtp_info['today_id'] = $sc_mtp['locations'];
//                    $mtp_info['task_of_the_day'] = $sc_mtp['task_of_the_day'];
                    $mtp[] = $mtp_info;
                }
            }
            #Task Of the Day
            $task = [];
            $q = "SELECT task, id from _task_of_the_day";
            $rq = mysqli_query($dbc, $q) or die(mysqli_error($dbc));
            if (mysqli_num_rows($rq) > 0) {
                $inc = 0;
                while ($dt = mysqli_fetch_assoc($rq)) {
                    $task[$inc]['id'] = $dt['id'];
                    $task[$inc]['name'] = $dt['task'];
                    $inc++;
                }
            }
/////////////////////SCHEME BUILT VALUE////////////////////////////////
            $sosdate = date('Y-m-d');
            $query_sos = "SELECT sos.scheme_id,scheme_name,start_date,end_date,value,value_to,scheme_gift FROM `scheme_on_sale` sos INNER JOIN scheme_on_sale_details sosd ON sos.`scheme_id`=sosd.scheme_id WHERE '$sosdate' BETWEEN `start_date` AND `end_date` AND intype='1'";
            $run_sos = mysqli_query($dbc, $query_sos) or die(mysqli_error($dbc));
            if (mysqli_num_rows($run_sos) > 0) {
                while ($sc_sos = mysqli_fetch_assoc($run_sos)) {
                    $sos_info['scheme_name'] = $sc_sos['scheme_name'];
                    $sos_info['start_date'] = $sc_sos['start_date'];
                    $sos_info['end_date'] = $sc_sos['end_date'];
                    $sos_info['value'] = $sc_sos['value'];
                    $sos_info['value_to'] = $sc_sos['value_to'];
                    $sos_info['scheme_per'] = $sc_sos['scheme_gift'];
                    $scheme_inbuilt[] = $sos_info;
                }
            }


////////////////////////////////////////////////////


////////////////////REAL ANK///////////////////////
            if ($row['sync_status'] == null || $row['sync_status'] == 0)
                $sync = '0';
///////////////////// FOR DEALER USER///////////////////
            if ($role_id == 5) {
                $essential[] = array("response" => "TRUE"
                , "user_id" => $person_id
                , "webview_status" => $webview_status
                , "webview_url" => $webview_url
                , "logged_in_town" => $loggedInTownArr
                , "mtd_target" => $mtd_target
                , "mtd_achievement" => $mtd_achievement
                , "emp_id" => $emp_id
                , "person_image" => $person_image
                , "state" => $state_name
                , "state_id" => $sid
                , "zone" => $zone_name
                , "zone_id" => $zid
                , "designation" => $role_name
                , "full_person_name" => $full_person_name
                , "person_contact" => $person_contact
                , "person_role" => $role_id
                , "dob" => $dob
                , "email" => $person_email
                , "dealer_state_id" => $dealer_state_id
                , "sync_status" => $sync
                , "target" => $dealer_target
                , "targetgraph" => $dealer_targetgraph
                , "category_sale_graph" => $catalogsale
                , "category_last_graph" => $lcatalogsale
                , "sale_order" => $sale
                , "invoice" => $chal
                , "constant_status" => $dconstant_data['constant_status']
                , "callwise_reporting_status" => $dconstant_data['callwise_reporting_status']
                , "dalilysales_summary_status" => $dconstant_data['dalilysales_summary_status']
                , "debug_on" => $dconstant_data['debug_on']
                , "track_at_sale" => $dconstant_data['track_at_sale']
                , "mtp_confirm_required" => $dconstant_data['mtp_confirm_required']
                , "mtp_extended_status" => $dconstant_data['mtp_extended_status']
                , "mtp_buffer_days" => $dconstant_data['mtp_buffer_days']
                , "dealer_id_fetch" => $dealer_id_fetch
                , "dealer_id_delete" => $dealer_id_delete
                , "dealer" => $final_dealer_details1
                , "retailer_id_fetch" => $retailer_id_fetch
                , "retailer_id_delete" => $retailer_id_delete
                , "retailer" => $run_chre_info
                , "beat_id_fetch" => $beat_id_fetch
                , "beat_id_delete" => $beat_id_delete
                , "beat" => $final_beat_details
                , "location_id_fetch" => $location_id_fetch
                , "location_id_delete" => $location_id_delete
                , "location" => $final_dealer_location_details
                , "town" => $town
                , "location1" => $loc1
                , "location2" => $loc2
                , "location3" => $loc3
                , "location4" => $loc4
                , "location5" => $loc5
                , "district" => $dist
                , "product_id_fetch" => $product_id_fetch
                , "product_id_delete" => $product_id_delete
                , "person" => $final_person_details1
                , "product_classification" => $final_product_classification_details
                , "product" => $final_catalog_product_details
                , "damage_product" => $final_damage_product_details
                , "complaint" => $final_comp_details
                , "user_category" => $final_compa_details
                , "category" => $final_category_details
                , "outlets" => $final_outlet_details
                , "outlet_categories" => $outlet_categories
                , "ownership_types" => $final_ownership_details
                , "field_experience" => $final_experience_details
                , "market_gift" => $final_retailer_gift
                , "travelling_modes" => $final_travel_deatails
                , "working_status" => $final_working_deatails
                , "isr_details" => $isr_array
                , "manual_attendance_usr" => $user_for_manual_attendance
                , "role" => $final_role_deatails
                , "tracking" => $stimedetails_info
                , "tracking_interval" => $time_info
                , "leave" => $leave
                , "scheme" => $scheme
                , "stock" => $stock
                , "scheme_value" => $scheme_inbuilt
                , "mtp" => $mtp
                , "task_of_the_day" => $task
                , "target_achieved" => $target_achieved
                , "complaint" => $complaint
                , "van" => $van
                , "threshold" => $threshold
                , "product_recommended" => $final_product_recommended
                , "gift" => $gift
                , "retailer_increment_id" => $retailer_increment_id
                , "daily_schedule" => $daily_schedule_details
                , "payment_mode" => $payment_mode
                , "working_with" => $ww
                , "tracking_constants" => $tracking_data
                , "show_mtp_approval_for" => $role_arr
                , "colleague" => $colleague
                );
            } else {
                $sync = '0';
                $essential[] = array("response" => "TRUE"
                , "user_id" => $person_id
                , "webview_status" => $webview_status
                , "webview_url" => $webview_url
                , "logged_in_town" => $loggedInTownArr
                , "mtd_target" => $mtd_target
                , "mtd_achievement" => $mtd_achievement
                , "emp_id" => $emp_code
                , "person_image" => $person_image
                , "state" => $state_name
                , "state_id" => $sid
                , "zone" => $zone_name
                , "zone_id" => $zid
                , "designation" => $role_name
                , "full_person_name" => $full_person_name
                , "person_contact" => $person_contact
                , "person_role" => $role_id
                , "dob" => $dob
                , "email" => $person_email
                , "dealer_state_id" => $dealer_state_id
                , "category_last_graph" => $lcatalogsale
                , "sync_status" => "0"
                , "constant_status" => $dconstant_data['constant_status']
                , "callwise_reporting_status" => $dconstant_data['callwise_reporting_status']
                , "dalilysales_summary_status" => $dconstant_data['dalilysales_summary_status']
                , "debug_on" => $dconstant_data['debug_on']
                , "track_at_sale" => $dconstant_data['track_at_sale']
                , "mtp_confirm_required" => $dconstant_data['mtp_confirm_required']
                , "mtp_extended_status" => $dconstant_data['mtp_extended_status']
                , "mtp_buffer_days" => $dconstant_data['mtp_buffer_days']
                , "dealer_id_fetch" => $dealer_id_fetch
                , "dealer_id_delete" => $dealer_id_delete
                , "dealer" => $final_dealer_details
                , "retailer_id_fetch" => $retailer_id_fetch
                , "retailer_id_delete" => $retailer_id_delete
                , "retailer" => $final_retailer_details
                , "beat_id_fetch" => $beat_id_fetch
                , "beat_id_delete" => $beat_id_delete
                , "beat" => $final_beat_details
                , "town" => $town
                , "location1" => $loc1
                , "location2" => $loc2
                , "location3" => $loc3
                , "location4" => $loc4
                , "location5" => $loc5
                , "district" => $dist
                , "location_id_fetch" => $location_id_fetch
                , "location_id_delete" => $location_id_delete
                , "location" => $final_dealer_location_details
                , "product_id_fetch" => $product_id_fetch
                , "product_id_delete" => $product_id_delete
                , "product_classification" => $final_product_classification_details
                , "product" => $final_catalog_product_details
                , "damage_product" => $final_damage_product_details
                , "complaint" => $final_comp_detathresholdils
                , "user_category" => $final_compa_details
                , "category" => $final_category_details
                , "outlets" => $final_outlet_details
                , "outlet_categories" => $outlet_categories
                , "ownership_types" => $final_ownership_details
                , "field_experience" => $final_experience_details
                , "market_gift" => $final_retailer_gift
                , "travelling_modes" => $final_travel_deatails
                , "working_status" => $final_working_deatails
                , "isr_details" => $isr_array
                , "manual_attendance_usr" => $user_for_manual_attendance
                , "role" => $final_role_deatails
                , "tracking" => $stimedetails_info
                , "tracking_interval" => $time_info
                , "leave" => $leave
                , "scheme" => $scheme
                , "stock" => $stock
                , "scheme_value" => $scheme_inbuilt
                , "mtp" => $mtp
                , "task_of_the_day" => $task
                , "retailer_target" => $retailer_target
                , "complaint" => $complaint
                , "gift" => $gift
                , "product_recommended" => $final_product_recommended
                , "retailer_increment_id" => $retailer_increment_id
                , "daily_schedule" => $daily_schedule_details
                , "payment_mode" => $payment_mode
                , "working_with" => $ww
                , "tracking_constants" => $tracking_data
                , "show_mtp_approval_for" => $role_arr
                , "colleague" => $colleague
                );
            }

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        } //if(empty($row['last_mobile_access_on'])) end here
        else if ($row['sync_status'] == '1') { // updated data
            if (!empty($dealer)) { //
                $q = "SELECT id,name FROM dealer d "
                    . "WHERE d.id IN ($dealer)";
                $r = mysqli_query($dbc, $q);

                while ($rows = mysqli_fetch_assoc($r)) {
                    $dealer_info['id'] = $rows['id'];
                    $dealer_info['name'] = $rows['name'];
                    $final_dealer_details[] = $dealer_info;
                }
            }
            if (!empty($retailer)) {
                $q = "SELECT id, firm_name, address,location_id FROM retailer r WHERE r.id IN ($retailer) ";
                $r = mysqli_query($dbc, $q);

                while ($rows = mysqli_fetch_assoc($r)) {
                    $retailer_info['id'] = $rows['id'];
                    $retailer_info['firm_name'] = $rows['firm_name'];
                    $retailer_info['location_id'] = $rows['location_id'];
                    $final_retailer_details[] = $dealer_info;
                }
            }
            if (!empty($beat)) { // here we used inner join form dealer location level attached
                $q = "SELECT l.name AS locname,l.id AS lid,dealer_id "
                    . "FROM user_dealer_retailer udr INNER "
                    . "JOIN dealer_location_rate_list USING(dealer_id) INNER "
                    . "JOIN location_$const_loc_level[dealer_level] AS l "
                    . "WHERE l.id IN ($beat) "
                    . "AND udr.user_id = '" . $row['id'] . "'";
//echo $q;
                $r = mysqli_query($dbc, $q);

                while ($rows = mysqli_fetch_assoc($r)) {
                    $beat_info['lid'] = $rows['lid'];
                    $beat_info['locname'] = $rows['locname'];
                    $beat_info['dealer_id'] = $rows['dealer_id'];
                    $final_beat_details[] = $beat_info;
                }
            }
            if (!empty($location)) { // here we used inner join form retailer location level attached
                $q1 = "SELECT l.name AS locname,l.id AS lid,dealer_id "
                    . "FROM user_dealer_retailer udr INNER "
                    . "JOIN dealer_location_rate_list USING(dealer_id) INNER "
                    . "JOIN location_$const_loc_level[dealer_level] AS l "
                    . "WHERE l.id IN ($location) "
                    . "AND udr.user_id = '" . $row['id'] . "'";
//echo $q1;
                $r1 = mysqli_query($dbc, $q1);
                while ($rows = mysqli_fetch_assoc($r1)) {
                    $dealer_location['lid'] = $rows['lid'];
                    $dealer_location['locname'] = $rows['locname'];
                    $dealer_location['dealer_id'] = $rows['dealer_id'];
                    $final_dealer_location_details[] = $dealer_location;
                }
            }
            $essential[] = array("response" => "TRUE"
            , "user_id" => $person_id
            , "webview_status" => $webview_status
            , "webview_url" => $webview_url
            , "logged_in_town" => $loggedInTownArr
            , "mtd_target" => $mtd_target
            , "mtd_achievement" => $mtd_achievement
            , "emp_id" => $emp_id
            , "person_image" => $person_image
            , "state" => $state_name
            , "state_id" => $sid
            , "zone" => $zone_name
            , "zone_id" => $zid
            , "designation" => $role_name
            , "sync_status" => $row['sync_status']
            , "constant_status" => $dconstant_data['constant_status']
            , "callwise_reporting_status" => $dconstant_data['callwise_reporting_status']
            , "dalilysales_summary_status" => $dconstant_data['dalilysales_summary_status']
            , "debug_on" => $dconstant_data['debug_on']
            , "track_at_sale" => $dconstant_data['track_at_sale']
            , "mtp_confirm_required" => $dconstant_data['mtp_confirm_required']
            , "mtp_extended_status" => $dconstant_data['mtp_extended_status']
            , "mtp_buffer_days" => $dconstant_data['mtp_buffer_days']
            , "dealer_id_fetch" => $dealer_id_fetch
            , "dealer_id_delete" => $dealer_id_delete
            , "dealer" => $final_dealer_details
            , "retailer_id_fetch" => $retailer_id_fetch
            , "retailer_id_delete" => $retailer_id_delete
            , "retailer" => $final_retailer_details
            , "beat_id_fetch" => $beat_id_fetch
            , "beat_id_delete" => $beat_id_delete
            , "beat" => $final_beat_details
            , "location_id_fetch" => $location_id_fetch
            , "location_id_delete" => $location_id_delete
            , "location" => $final_dealer_location_details
            , "product_id_fetch" => $product_id_fetch
            , "product_id_delete" => $product_id_delete
            , "product_classification" => $final_product_classification_details
            , "product" => $final_catalog_product_details
            , "catalog_id_fetch" => $catalog_id_fetch
            , "catalog_id_delete" => $catalog_id_delete
            , "category" => $final_category_details
            , "outlets" => $final_outlet_details
            , "outlet_categories" => $outlet_categories
            , "ownership_types" => $final_ownership_details
            , "field_experience" => $final_experience_details
            , "market_gift" => $final_retailer_gift
            , "travelling_modes" => $final_travel_deatails
            , "working_status" => $final_working_deatails
            , "role" => $final_role_deatails
            , "tracking" => $stimedetails_info
            , "tracking_interval" => $time_info
            , "retailer_increment_id" => $retailer_increment_id
            , "daily_schedule" => $daily_schedule_details
            , "payment_mode" => $payment_mode
            , "working_with" => $ww
            , "tracking_constants" => $tracking_data
            , "show_mtp_approval_for" => $role_arr
            , "colleague" => $colleague
            );

        } //$row['sync_status'] == 1 end here
        else if ($row['sync_status'] == 2) {
            $essential[] = array("response" => "TRUE"
            , "sync_status" => $row['sync_status']
            , "tracking_constant" => $tracking_constant
            );
        }
    } else {
        $essential[] = array("response" => "FALSE");
    }
    $final_array = array("result" => $essential);
// pre($final_array);
// exit();
    $data = json_encode($final_array);
// echo $data;
    $file = fopen("signin_13version_data/" . $get_user_id . ".php", "w") or die("Unable to open file!");
    fwrite($file, $data);
    $myfile = $get_user_id . ".php";
    fclose($file);
    chmod("signin_13version_data/" . $get_user_id . ".php", 0777);
///echo $file."".$get_user_id;
//}
/// if(!empty($action_status))
//echo $data;
} else {
    $essential[] = array("response" => "FALSE");

    $data = json_encode(array("result" => $essential));
}

echo $data;


?>
