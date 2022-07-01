<?php
include "../admin/include/generate_data.php";
require_once('../admin/functions/common_function.php');
require_once('../admin/include/conectdb.php');
require_once('../admin/include/config.inc.php');
require_once('../admin/include/my-functions.php');
error_reporting(1);
// echo 'h'; die;
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
$get_user_id = $user_res['person_id'];
$is_mtp_enabled = $user_res['is_mtp_enabled'];
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


        $inc = 1;
   
        



//require_once('query.php');
// default query for insert data in catalog_product_list
        $essential = array();
        $dealer_info = array();
        $dealer_array = array();
        $dealer_location = array();
     
        $beat_array = array();

        $retailer_info = array();
  
        $beat_info = array();
     
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
    
        $retailer_gift = array();
    
        $travel = array();
   
        $final_working_deatails = array();
        $working = array();
        $mstatusarray = array();
        $final_mstatus_details = array();
        $competator_info = array();
        $leave = array();
    

     
       

        $gift = array();
  
        $person_info = array();
      

    

   
        $user_retrailer_incrementid = 0;
        $constant_values = "";
        $daily_schedule_details = array();
        $payment_mode = array();
       

        $webview_status=0;
        $webview_url='';
        

        


 



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

        function getJuniorList($id,$dbc,$j=0)
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
                    if ($id>1)
                    {
                        getJuniorList($row['id'],$dbc,$j);
                    }
                }
            }

            return $_SESSION['idArr2'];
        }

        // $ox2=getJuniorList($id,$dbc);
        // $test['junior']=!empty($ox2)?$ox2:[];

        // $colleArr=[0=>['id'=>'0','name'=>'SELF']];
        // $collegue_count=1;
        // foreach ($test['junior'] as $tx)
        // {
        //     $colleArr[$collegue_count]=$tx;
        //     $collegue_count++;
        // }

        // foreach ($test['senior'] as $tt)
        // {
        //     $colleArr[$collegue_count]=$tt;
        //     $collegue_count++;
        // }

        // $colleague=$colleArr;
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






        $query_constant_level = "SELECT * FROM `_constant` WHERE company_id=1";
        $run_constant_level = mysqli_query($dbc, $query_constant_level);
        $dconstant_data = mysqli_fetch_assoc($run_constant_level);
       

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


        $qoutlet = "SELECT id, outlet_type FROM _retailer_outlet_type WHERE `status` = '1' ORDER BY `sequence`";
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
        $query = "SELECT id, outlet_category FROM _retailer_outlet_category where status=1  ORDER BY `sequence`";
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

//here we get user person details
        $query_person = "SELECT p.id AS rId, CONCAT_WS(' ',first_name,middle_name,last_name) AS full_name,mobile,address,dob FROM person p INNER JOIN person_details pd ON pd.person_id = p.id WHERE p.id = $row[id]";
        $run_person = mysqli_query($dbc, $query_person);
        $fetch_person = mysqli_fetch_assoc($run_person);
        $id = $fetch_person['rId'];
        $person_info[] = $fetch_person;
        $town = [];

// user person details end here
        if ($row['sync_status'] == '0') { // here we get all data
//This query is used to fetch dealer information
           

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
               
            }

            // if (!empty($beat_array)) 
            // {
//This query is used to fetch location of the dealer in other words we can say that here we find out beat end here //
                $reatiler_loc_array = array();
                $locationarray = implode(",", $beat_array);

                $dlevel = $dconstant_data['dealer_level'];
                $rlevel = $dconstant_data['retailer_level'];

                $querylocation = "SELECT dealer_id,location_id AS location,location_$rlevel.name FROM dealer_location_rate_list
                INNER JOIN location_$rlevel ON location_$dlevel.id = dealer_location_rate_list.location_id WHERE dealer_location_rate_list.user_id=$get_user_id GROUP BY location DESC";

                // $querylocation = "SELECT dealer_id,location_$rlevel.id AS location,location_$rlevel.name FROM location_$dlevel";
                // for ($i = $dlevel; $i < $rlevel; $i++) 
                // {
                //     $j = $i + 1;
                //     $querylocation .= " INNER JOIN location_$j ON location_$j.location_" . $i . "_id = location_$i.id ";
                // }
                // $querylocation .= " INNER JOIN dealer_location_rate_list ON location_$dlevel.id = dealer_location_rate_list.location_id WHERE location_$dlevel.id IN ($locationarray) AND dealer_id IN ($dealerarray) GROUP BY location DESC";

               // echo $querylocation;die;

                $rqlocation = mysqli_query($dbc, $querylocation) or die(mysqli_error($dbc));
                if (mysqli_num_rows($rqlocation) > 0) {
                    while ($dlocation = mysqli_fetch_assoc($rqlocation)) {
                        $dealer_location['dealer_id'] = $dlocation['dealer_id'];
                        $dealer_location['id'] = $dlocation['location'];
                        $reatiler_loc_array[] = $dlocation['location'];
                        $dealer_location['name'] = $dlocation['name'];

                    }
                }
           // } //if(!empty($beat_array)) end here
//here some confusion and i need to ask sujeet sir..
            if (!empty($reatiler_loc_array))
                $reatiler_loc_array_str = implode(',', $reatiler_loc_array);
            else
                $reatiler_loc_array_str = '';
            $dealerarray = implode(",", $dealer_array); 
            // $query_retailer_access_on = "SELECT udr.seq_id,r.id,r.email as email1,r.landline,r.contact_per_name,r.other_numbers,r.name,location_id,rl.name AS loc_name,r.address,r.tin_no as tin,lat_long FROM retailer r 
            // INNER JOIN user_dealer_retailer udr ON udr.retailer_id = r.id 
            // INNER JOIN location_$dconstant_data[retailer_level] AS rl ON rl.id = r.location_id 
            // WHERE user_id = '$row[id]' AND r.location_id IN ($reatiler_loc_array_str) AND retailer_status='1' GROUP BY id DESC";
      
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


        

       


//final_isr_array
            $idealer_data = "SELECT GROUP_CONCAT(DISTINCT dealer_id ORDER BY dealer_id ASC SEPARATOR ',') as dealer_id FROM user_dealer_retailer where user_id='$person_id' ";
// h1($idealer_data);
            $idlr_query = mysqli_query($dbc, $idealer_data);
            $idlr_row = mysqli_fetch_object($idlr_query);
            $idealer_id = $idlr_row->dealer_id;

// $isr_data = "SELECT rolename,person.id,CONCAT_WS(' ',first_name,middle_name,last_name) as name,dealer_id,dealer.name as dealer_name FROM person INNER JOIN _role ON _role.role_id=person.role_id INNER JOIN user_dealer_retailer udr ON udr.user_id=person.id INNER JOIN dealer ON udr.dealer_id=dealer.id where dealer_id IN($idealer_id) and (person.role_id='14' OR person.role_id='15') group by dealer_id,person.id";






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

///////////////////// FOR DEALER USER///////////////////
            if ($role_id == 5) {
                $essential[] = array("response" => "TRUE"
                , "user_id" => $person_id
                , "is_mtp_enabled" => $is_mtp_enabled
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
                , "target" => $dealer_target
                , "targetgraph" => $dealer_targetgraph
                , "category_sale_graph" => $catalogsale
                , "sale_order" => $sale
                , "invoice" => $chal
                , "retailer" => $run_chre_info
                , "town" => $town
                , "person" => $final_person_details1
                , "product_classification" => $final_product_classification_details
                , "product" => $final_catalog_product_details
                , "category" => $final_category_details
                , "outlets" => $final_outlet_details
                , "outlet_categories" => $outlet_categories
                , "ownership_types" => $final_ownership_details
                , "working_status" => $final_working_deatails
                , "role" => $final_role_deatails
                , "leave" => $leave
                , "task_of_the_day" => $task
                , "target_achieved" => $target_achieved
                , "van" => $van
                , "threshold" => $threshold
                , "gift" => $gift        
                , "daily_schedule" => $daily_schedule_details
                , "payment_mode" => $payment_mode
                , "colleague" => $colleague
             
                );
            } else {

                $essential[] = array("response" => "TRUE"
                , "user_id" => $person_id
                , "is_mtp_enabled" => $is_mtp_enabled
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
                , "town" => $town
                , "product_classification" => $final_product_classification_details
                , "product" => $final_catalog_product_details
                , "category" => $final_category_details
                , "outlets" => $final_outlet_details
                , "outlet_categories" => $outlet_categories
                , "ownership_types" => $final_ownership_details
                , "working_status" => $final_working_deatails
                , "role" => $final_role_deatails
                , "leave" => $leave
                , "task_of_the_day" => $task
                , "gift" => $gift
                , "daily_schedule" => $daily_schedule_details
                , "payment_mode" => $payment_mode
                , "colleague" => $colleague
                
                );
            }

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
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
