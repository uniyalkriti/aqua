<?php
	//require_once('../../admin/functions/common_function.php');
require_once('conectdb.php');
require_once('config.inc.php');
require_once('my-functions.php');


function generate_signin_data($get_user_id){

global $dbc;
//print_r($dbc);die('hjklkj');

$myobj = new mtp();
//echo $get_user_id;die;
/*$imei = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['imei'])));
$uname = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['uname'])));
$pass = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['pass'])));*/
$id = $get_user_id;
//echo $id;die; 
$rest = substr($imei, -5) * 1; // multiplying by 1 for termination starting zero values

$query_person_login = "SELECT * FROM `person_login` INNER JOIN person ON person_login.person_id = person.id AND person.id='$id' AND person_status = '1'  ORDER BY person_username ASC";
//echo $query_person_login;die;
//h1($query_person_login);

$user_qry = mysqli_query($dbc, $query_person_login);
//$user_res=  mysqli_fetch_assoc($user_qry);
$company_id = 1;
if ($user_qry && mysqli_num_rows($user_qry) > 0) {
//$tracking = $myobj->tracking_mobile_data($filter="person_username = '$uname' AND person_password = '$pass' AND imei_number = '$imei' AND person_status = '1' ",  $records = '', $orderby='ORDER BY person_username ASC');
    $tracking_constant = array();
    $tracking = $myobj->get_constant_datas_list($filter = "", $records = '', $orderby = '');

    $tracking = $tracking[1];
    $tracking_constant[] = $tracking;
//pre($tracking_constant);
    $tracking_interval = explode(',', $tracking['tracking_intervals']);

    $inc = 1;
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
    $final_dealer_details = array();
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

    $person_info = array();
    $final_damage_product_details = array();
    $user_retrailer_incrementid = 0;
    $constant_values = "";

    $query_constant_level = "SELECT * FROM `_constant` WHERE company_id=1";
    $run_constant_level = mysqli_query($dbc, $query_constant_level);
    $dconstant_data = mysqli_fetch_assoc($run_constant_level);
    $dconstant_data;
    if ($dconstant_data['constant_status'] == 1) {
        //    $constant_data = $dconstant_data;
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
    $qoutlet = "SELECT id, outlet_type FROM _retailer_outlet_type";
    $routlet = mysqli_query($dbc, $qoutlet);
    if ($routlet) {
        while ($doutlet = mysqli_fetch_assoc($routlet)) {
            $outlet['id'] = $doutlet['id'];
            $outlet['outlet_type'] = $doutlet['outlet_type'];
            $final_outlet_details[] = $outlet;
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
    $qtravel = "SELECT id, mode FROM _travelling_mode";
    $rtravel = mysqli_query($dbc, $qtravel);
    if ($rtravel) {
        while ($dtravel = mysqli_fetch_assoc($rtravel)) {
            $travel['id'] = $dtravel['id'];
            $travel['mode'] = $dtravel['mode'];
            $final_travel_deatails[] = $travel;
        }
    }

    
    $qworking = "SELECT id,name,parent_id FROM _working_status";
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
    $role_id = $row['role_id'];
    $state_id = $row['state_id'];
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


//here we get user person details
    $query_person = "SELECT p.id AS rId, CONCAT_WS(' ',first_name,middle_name,last_name) AS full_name,mobile,address,dob FROM person p INNER JOIN person_details pd ON pd.person_id = p.id WHERE p.id = $row[id]";
    $run_person = mysqli_query($dbc, $query_person);
    $fetch_person = mysqli_fetch_assoc($run_person);
    $id = $fetch_person['rId'];
    $person_info[] = $fetch_person;
    // user person details end here
    if ($row['sync_status'] == '0') { // here we get all data
        //This query is used to fetch dealer information
        $query_dealer = "SELECT d.id,d.name FROM dealer AS d INNER JOIN user_dealer_retailer AS udr ON udr.dealer_id = d.id AND udr.user_id = '$row[id]' GROUP BY id DESC"; //user_id 

        $run_dealer = mysqli_query($dbc, $query_dealer);
        if (mysqli_num_rows($run_dealer) > 0) {
            while ($dealer_fetch = mysqli_fetch_assoc($run_dealer)) {
                $dealer_info['id'] = $dealer_fetch['id'];
                $dealer_info['name'] = $dealer_fetch['name'];
                //$dealer_info['ss_id'] = $dealer_fetch['csa_id'];
                $dealer_array[] = $dealer_fetch['id'];
                $final_dealer_details[] = $dealer_info;
            }
        }

        if (!empty($dealer_array)) {
            //This dealer information end here
            $dealerarray = implode(",", $dealer_array); // here we get all the dealer id
            //This query is used to fetch location of the dealer in other words we can say that here we find out beat
            $query_locality = "SELECT l.name AS locname,l.id AS lid,dealer_id "
                    . "FROM dealer_location_rate_list AS dl INNER "
                    . "JOIN location_$dconstant_data[dealer_level] AS l "
                    . "ON l.id = dl.location_id "
                    . "WHERE dl.dealer_id in ($dealerarray)"; //here we get beat 
            //echo $query_locality; exit();
            $run_locality = mysqli_query($dbc, $query_locality) or die(mysqli_error($dbc));
            if (mysqli_num_rows($run_locality) > 0) {
                while ($locality_fetch = mysqli_fetch_assoc($run_locality)) {
                    $beat_info['id'] = $locality_fetch['lid'];
                    $beat_array[] = $locality_fetch['lid'];
                    $beat_info['name'] = $locality_fetch['locname'];
                    $beat_info['dealer_id'] = $locality_fetch['dealer_id'];
                    $final_beat_details[] = $beat_info;
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

            $rqlocation = mysqli_query($dbc, $querylocation) or die(mysqli_error($dbc));
            if (mysqli_num_rows($rqlocation) > 0) {
                while ($dlocation = mysqli_fetch_assoc($rqlocation)) {
                    $dealer_location['dealer_id'] = $dlocation['dealer_id'];
                    $dealer_location['id'] = $dlocation['location'];
                    $reatiler_loc_array[] = $dlocation['location'];
                    $dealer_location['name'] = $dlocation['name'];
                    $final_dealer_location_details[] = $dealer_location;
                }
            }
        } //if(!empty($beat_array))  end here
        //here some confusion and i need to ask sujeet sir..
        if (!empty($reatiler_loc_array))
            $reatiler_loc_array_str = implode(',', $reatiler_loc_array);
        else
            $reatiler_loc_array_str = '';
        $query_retailer_access_on = "SELECT r.id,r.name,location_id,rl.name AS loc_name,r.address FROM retailer r INNER JOIN user_dealer_retailer udr ON udr.retailer_id = r.id INNER JOIN location_$dconstant_data[retailer_level] AS rl ON rl.id = r.location_id WHERE user_id = '$row[id]' AND r.location_id IN ($reatiler_loc_array_str) GROUP BY id DESC"; //h1($query_retailer_access_on);

        $run_retailer = mysqli_query($dbc, $query_retailer_access_on);
        if (mysqli_num_rows($run_retailer) > 0) {
            while ($retailer_fetch = mysqli_fetch_assoc($run_retailer)) {
                $retailer_info['id'] = $retailer_fetch['id'];
                $retailer_info['name'] = $retailer_fetch['name'].' ['.$retailer_fetch['address'].']';
                $retailer_info['location_id'] = $retailer_fetch['location_id'];
                $retailer_info['loc_name'] = $retailer_fetch['loc_name'];
                $final_retailer_details[] = $retailer_info;
            }
        }
        //This query is Used to send catalog_product name list

           $query_catalog_product = "SELECT catalog_product.id,catalog_id,unit,catalog_product.name,cprl.base_price,cprl.rate as mrp,catalog_2.name as cname FROM catalog_product "
                    . " INNER JOIN catalog_product_rate_list cprl ON catalog_product.id = cprl.catalog_product_id"
                   . " INNER JOIN catalog_2 ON catalog_2.id=catalog_product.catalog_id  WHERE stateId='$state_id' ORDER BY name ASC";
            $run_catalog_product = mysqli_query($dbc, $query_catalog_product);
            if (mysqli_num_rows($run_catalog_product) > 0) {
                while ($catalog_product_fetch = mysqli_fetch_assoc($run_catalog_product)) {
                    
                    $catalog_product_info['id'] = $catalog_product_fetch['id'];
                    $catalog_product_info['category'] = $catalog_product_fetch['catalog_id'];
                    $catalog_product_info['category_name'] = $catalog_product_fetch['cname'];
                    $catalog_product_info['name'] = $catalog_product_fetch['name'];
                    $catalog_product_info['base_price'] = $catalog_product_fetch['base_price'];
                    $catalog_product_info['mrp'] = $catalog_product_fetch['mrp'];
                    $catalog_product_info['unit'] = $catalog_product_fetch['unit'];
                    $final_catalog_product_details[] = $catalog_product_info;
                }
            } // if(mysqli_num_rows($run_catalog_product)if(mysqli_num_rows($run_catalog_product)
        
            
        $query_damage_product = "SELECT id,`name` as catalog_product,base_price as rate,'1' as state_id FROM catalog_product";
                
    $run_damage_product = mysqli_query($dbc, $query_damage_product) or die(mysqli_error($dbc));
        
        while($damage_product_fetch = mysqli_fetch_assoc($run_damage_product))
                {
                   $damage_product_info['id'] = $damage_product_fetch['id'];
                   $damage_product_info['catalog_product'] =$damage_product_fetch['catalog_product'];

                   $damage_product_info['rate'] =$damage_product_fetch['rate'];
                   $final_damage_product_details[] = $damage_product_info;
                }
    // h1($query_damage_product);
  
        $comp=array();
    $final_comp_details=array();
        $compa=array();
    $final_compa_details=array();
    $cdata = "SELECT id, name FROM complaint_type";
           //h1($cdata);
         $cquery = mysqli_query($dbc,$cdata);
         if($cquery)
         {
             while($complain = mysqli_fetch_assoc($cquery))
             {
                 $comp['id'] = $complain['id'];
                 $comp['name'] = $complain['name'];
                 $final_comp_details[] = $comp;
             }
         }

    $cdataa = "SELECT id, name FROM user_category";
        $cquerya = mysqli_query($dbc,$cdataa);
         if($cquerya)
         {
             while($complaina = mysqli_fetch_assoc($cquerya))
             {
                 $compa['id'] = $complaina['id'];
                 $compa['name'] = $complaina['name'];
                 $final_compa_details[] = $compa;
             }
         }
        //This query is used to send top level category list
        $query_category = "SELECT id,name FROM catalog_2 ORDER BY id ASC";
        $run_category = mysqli_query($dbc, $query_category) or die(mysqli_error($dbc));
        if (mysqli_num_rows($run_category) > 0) {
            while ($category_fetch = mysqli_fetch_assoc($run_category)) {
                $category_info['id'] = $category_fetch['id'];
                $category_info['name'] = $category_fetch['name'];
                $final_category_details[] = $category_info;
            }
        }
////////////////////////////////ITS NOT USING////////////////////////////////
        $essential[] = array("response" => "TRUE"
            , "user_id" => $person_id
            , "person_role" => $role_id
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
            , "product" => $final_catalog_product_details
            , "damage_product"=>$final_damage_product_details
            , "complaint"=>$final_comp_details
            , "user_category"=>$final_compa_details
            , "category" => $final_category_details
            , "outlets" => $final_outlet_details
            , "ownership_types" => $final_ownership_details
            , "field_experience" => $final_experience_details
            , "market_gift" => $final_retailer_gift
            , "travelling_modes" => $final_travel_deatails
            , "working_status" => $final_working_deatails
            , "role" => $final_role_deatails
            , "tracking" => $stimedetails_info
            , "tracking_interval" => $time_info
            , "retailer_increment_id" => $retailer_increment_id
        );
    } //if(empty($row['last_mobile_access_on'])) end here
    else if ($row['sync_status'] == '1') { // updated data
        if (!empty($dealer)) { //
            $q = "SELECT id,name FROM dealer d  "
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
                    . "AND udr.user_id = '" . $row['id'] . "' GROUP BY locname ASC";

            $r = mysqli_query($dbc, $q);

            while ($rows = mysqli_fetch_assoc($r)) {
                $beat_info['lid'] = $rows['lid'];
                $beat_info['locname'] = $rows['locname'];
                $beat_info['dealer_id'] = $rows['dealer_id'];
                $final_beat_details[] = $beat_info;
            }
        }
        if (!empty($location)) { // here we used inner join form retailer location level attached
            $q = "SELECT l.name AS locname,l.id AS lid,dealer_id "
                    . "FROM user_dealer_retailer udr INNER "
                    . "JOIN dealer_location_rate_list USING(dealer_id) INNER "
                    . "JOIN location_$const_loc_level[retailer_level] AS l "
                    . "WHERE l.id IN ($location) "
                    . "AND udr.user_id = '" . $row['id'] . "' GROUP BY locname ASC";
            $r = mysqli_query($dbc, $q);
            while ($rows = mysqli_fetch_assoc($r)) {
                $dealer_location['lid'] = $rows['lid'];
                $dealer_location['locname'] = $rows['locname'];
                $dealer_location['dealer_id'] = $rows['dealer_id'];
                $final_dealer_location_details[] = $dealer_location;
            }
        }
        $essential[] = array("response" => "TRUE"
            , "user_id" => $person_id
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
            , "product" => $final_catalog_product_details
            , "catalog_id_fetch" => $catalog_id_fetch
            , "catalog_id_delete" => $catalog_id_delete
            , "category" => $final_category_details
            , "outlets" => $final_outlet_details
            , "ownership_types" => $final_ownership_details
            , "field_experience" => $final_experience_details
            , "market_gift" => $final_retailer_gift
            , "travelling_modes" => $final_travel_deatails
            , "working_status" => $final_working_deatails
            , "role" => $final_role_deatails
            , "tracking" => $stimedetails_info
            , "tracking_interval" => $time_info
            , "retailer_increment_id" => $retailer_increment_id
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
//        pre($final_array);
//        exit();
//echo $get_user_id;die;
$data = json_encode($final_array);
/*if (!file_exists($get_user_id.".php")) { 
    die('File does not exist');
}else{

	die('exist');
}*/
//echo $get_user_id;die;
	$file = fopen("../../../webservices/signin_data/".$get_user_id.".php","w") or die('file not open ');

	fwrite($file,$data) or die('file not write');
	$myfile = $get_user_id.".php";
	fclose($file);
	chmod("../../../webservices/signin_data/".$get_user_id.".php", 0777);
	

return $data;
}

function signin_new_data_generate($get_user_id){
	global $dbc;



$myobj = new mtp();
/*$imei = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['imei'])));
$uname = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['uname'])));
$pass = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['pass'])));*/
$id = mysqli_real_escape_string($dbc, trim(stripslashes($get_user_id)));
$rest = substr($imei, -5) * 1; // multiplying by 1 for termination starting zero values

$query_person_login = "SELECT * FROM `person_login` INNER JOIN person ON person_login.person_id = person.id AND person.id='$id' AND person_status = '1'  ORDER BY person_username ASC";
//h1($query_person_login);
$user_qry = mysqli_query($dbc, $query_person_login);
//$user_res=  mysqli_fetch_assoc($user_qry);
$company_id = 1;
if ($user_qry && mysqli_num_rows($user_qry) > 0) {
//$tracking = $myobj->tracking_mobile_data($filter="person_username = '$uname' AND person_password = '$pass' AND imei_number = '$imei' AND person_status = '1' ",  $records = '', $orderby='ORDER BY person_username ASC');
    $tracking_constant = array();
    $tracking = $myobj->get_constant_datas_list($filter = "", $records = '', $orderby = '');

    $tracking = $tracking[1];
    $tracking_constant[] = $tracking;
//pre($tracking_constant);
    $tracking_interval = explode(',', $tracking['tracking_intervals']);

    $inc = 1;
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
    $final_dealer_details = array();
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

    $person_info = array();
    $final_damage_product_details = array();
    $final_isr_array = array();
    $user_for_manual_attendance = array();
    $user_retrailer_incrementid = 0;
    $constant_values = "";

    $query_constant_level = "SELECT * FROM `_constant` WHERE company_id=1";
    $run_constant_level = mysqli_query($dbc, $query_constant_level);
    $dconstant_data = mysqli_fetch_assoc($run_constant_level);
    $dconstant_data;
    if ($dconstant_data['constant_status'] == 1) {
        //    $constant_data = $dconstant_data;
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
    $qoutlet = "SELECT id, outlet_type FROM _retailer_outlet_type";
    $routlet = mysqli_query($dbc, $qoutlet);
    if ($routlet) {
        while ($doutlet = mysqli_fetch_assoc($routlet)) {
            $outlet['id'] = $doutlet['id'];
            $outlet['outlet_type'] = $doutlet['outlet_type'];
            $final_outlet_details[] = $outlet;
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
    $role_id = $row['role_id'];
    $state_id = $row['state_id'];
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


//here we get user person details
    $query_person = "SELECT p.id AS rId, CONCAT_WS(' ',first_name,middle_name,last_name) AS full_name,mobile,address,dob FROM person p INNER JOIN person_details pd ON pd.person_id = p.id WHERE p.id = $row[id]";
    $run_person = mysqli_query($dbc, $query_person);
    $fetch_person = mysqli_fetch_assoc($run_person);
    $id = $fetch_person['rId'];
    $person_info[] = $fetch_person;
    // user person details end here
    if ($row['sync_status'] == '0') { // here we get all data
        //This query is used to fetch dealer information
        $query_dealer = "SELECT d.id,d.name FROM dealer AS d INNER JOIN user_dealer_retailer AS udr ON udr.dealer_id = d.id AND udr.user_id = '$row[id]' GROUP BY id DESC"; //user_id 

        $run_dealer = mysqli_query($dbc, $query_dealer);
        if (mysqli_num_rows($run_dealer) > 0) {
            while ($dealer_fetch = mysqli_fetch_assoc($run_dealer)) {
                $dealer_info['id'] = $dealer_fetch['id'];
                $dealer_info['name'] = $dealer_fetch['name'];
                //$dealer_info['ss_id'] = $dealer_fetch['csa_id'];
                $dealer_array[] = $dealer_fetch['id'];
                $final_dealer_details[] = $dealer_info;
            }
        }

        if (!empty($dealer_array)) {
            //This dealer information end here
            $dealerarray = implode(",", $dealer_array); // here we get all the dealer id
            //This query is used to fetch location of the dealer in other words we can say that here we find out beat
            $query_locality = "SELECT l.name AS locname,l.id AS lid,dealer_id "
                    . "FROM dealer_location_rate_list AS dl INNER "
                    . "JOIN location_$dconstant_data[dealer_level] AS l "
                    . "ON l.id = dl.location_id "
                    . "WHERE dl.dealer_id in ($dealerarray)"; //here we get beat 
            //echo $query_locality; exit();
            $run_locality = mysqli_query($dbc, $query_locality) or die(mysqli_error($dbc));
            if (mysqli_num_rows($run_locality) > 0) {
                while ($locality_fetch = mysqli_fetch_assoc($run_locality)) {
                    $beat_info['id'] = $locality_fetch['lid'];
                    $beat_array[] = $locality_fetch['lid'];
                    $beat_info['name'] = $locality_fetch['locname'];
                    $beat_info['dealer_id'] = $locality_fetch['dealer_id'];
                    $final_beat_details[] = $beat_info;
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

            $rqlocation = mysqli_query($dbc, $querylocation) or die(mysqli_error($dbc));
            if (mysqli_num_rows($rqlocation) > 0) {
                while ($dlocation = mysqli_fetch_assoc($rqlocation)) {
                    $dealer_location['dealer_id'] = $dlocation['dealer_id'];
                    $dealer_location['id'] = $dlocation['location'];
                    $reatiler_loc_array[] = $dlocation['location'];
                    $dealer_location['name'] = $dlocation['name'];
                    $final_dealer_location_details[] = $dealer_location;
                }
            }
        } //if(!empty($beat_array))  end here
        //here some confusion and i need to ask sujeet sir..
        if (!empty($reatiler_loc_array))
            $reatiler_loc_array_str = implode(',', $reatiler_loc_array);
        else
            $reatiler_loc_array_str = '';
        $query_retailer_access_on = "SELECT r.id,r.name,location_id,rl.name AS loc_name,r.address FROM retailer r INNER JOIN user_dealer_retailer udr ON udr.retailer_id = r.id INNER JOIN location_$dconstant_data[retailer_level] AS rl ON rl.id = r.location_id WHERE user_id = '$row[id]' AND r.location_id IN ($reatiler_loc_array_str) GROUP BY id DESC"; //h1($query_retailer_access_on);

        $run_retailer = mysqli_query($dbc, $query_retailer_access_on);
        if (mysqli_num_rows($run_retailer) > 0) {
            while ($retailer_fetch = mysqli_fetch_assoc($run_retailer)) {
                $retailer_info['id'] = $retailer_fetch['id'];
                $retailer_info['name'] = $retailer_fetch['name'].' ['.$retailer_fetch['address'].']';
                $retailer_info['location_id'] = $retailer_fetch['location_id'];
                $retailer_info['loc_name'] = $retailer_fetch['loc_name'];
                $final_retailer_details[] = $retailer_info;
            }
        }
        //This query is Used to send catalog_product name list

           $query_catalog_product = "SELECT catalog_product.id,catalog_id,unit,catalog_product.name,cprl.base_price,cprl.rate as mrp,catalog_2.name as cname FROM catalog_product "
                    . " INNER JOIN catalog_product_rate_list cprl ON catalog_product.id = cprl.catalog_product_id"
                   . " INNER JOIN catalog_2 ON catalog_2.id=catalog_product.catalog_id  WHERE stateId='$state_id' ORDER BY name ASC";
            $run_catalog_product = mysqli_query($dbc, $query_catalog_product);
            if (mysqli_num_rows($run_catalog_product) > 0) {
                while ($catalog_product_fetch = mysqli_fetch_assoc($run_catalog_product)) {
                    
                    $catalog_product_info['id'] = $catalog_product_fetch['id'];
                    $catalog_product_info['category'] = $catalog_product_fetch['catalog_id'];
                    $catalog_product_info['category_name'] = $catalog_product_fetch['cname'];
                    $catalog_product_info['name'] = $catalog_product_fetch['name'];
                    $catalog_product_info['base_price'] = $catalog_product_fetch['base_price'];
                    $catalog_product_info['mrp'] = $catalog_product_fetch['mrp'];
                    $catalog_product_info['unit'] = $catalog_product_fetch['unit'];
                    $final_catalog_product_details[] = $catalog_product_info;
                }
            } // if(mysqli_num_rows($run_catalog_product)if(mysqli_num_rows($run_catalog_product)
        
            
        $query_damage_product = "SELECT id,`name` as catalog_product,base_price as rate,'1' as state_id FROM catalog_product";
                
    $run_damage_product = mysqli_query($dbc, $query_damage_product) or die(mysqli_error($dbc));
        
        while($damage_product_fetch = mysqli_fetch_assoc($run_damage_product))
                {
                   $damage_product_info['id'] = $damage_product_fetch['id'];
                   $damage_product_info['catalog_product'] =$damage_product_fetch['catalog_product'];

                   $damage_product_info['rate'] =$damage_product_fetch['rate'];
                   $final_damage_product_details[] = $damage_product_info;
                }
    // h1($query_damage_product);
  
        $comp=array();
    $final_comp_details=array();
        $compa=array();
    $final_compa_details=array();
    $cdata = "SELECT id, name FROM complaint_type";
           //h1($cdata);
         $cquery = mysqli_query($dbc,$cdata);
         if($cquery)
         {
             while($complain = mysqli_fetch_assoc($cquery))
             {
                 $comp['id'] = $complain['id'];
                 $comp['name'] = $complain['name'];
                 $final_comp_details[] = $comp;
             }
         }

     //final_isr_array   
         $idealer_data = "SELECT GROUP_CONCAT(DISTINCT dealer_id ORDER BY dealer_id ASC SEPARATOR ',') as dealer_id FROM user_dealer_retailer where user_id='$row[id]' ";
         // h1($idealer_data);die;
         $idlr_query = mysqli_query($dbc,$idealer_data);
         $idlr_row =mysqli_fetch_object($idlr_query);
        $idealer_id =$idlr_row->dealer_id;

     $isr_data = "SELECT rolename,person.id,CONCAT_WS(' ',first_name,middle_name,last_name) as name,dealer_id,dealer.name as dealer_name FROM person INNER JOIN _role ON _role.role_id=person.role_id INNER JOIN user_dealer_retailer udr ON udr.user_id=person.id INNER JOIN dealer ON udr.dealer_id=dealer.id where dealer_id IN($idealer_id) and (person.role_id='14' OR person.role_id='15') group by dealer_id,person.id";
       // h1($isr_data);
         $isr_query = mysqli_query($dbc,$isr_data);
         if($isr_query)
         {
             while($isr = mysqli_fetch_assoc($isr_query))
             {
                 $isr_detail['id'] = $isr['id'];
                 $isr_detail['isr_name'] = $isr['name'].'/'.$isr['rolename'];
                 $isr_detail['isr_dealer_id'] = $isr['dealer_id'];
                 $isr_detail['isr_dealer_name'] = $isr['dealer_name'];
                 $isr_array[] = $isr_detail;
             }
             //pre($final_isr_array);die;
         }

        //user_for_manual_attendance
         $user_for_manual_attendance = $myobj->recursiveall2_signin($row[id]);
        $user_for_manual_attendance= isset($_SESSION['juniordata'])?$_SESSION['juniordata']:array();
         
         
        $final_isr_array=(array_merge($isr_array,$user_for_manual_attendance));
        $final_isr_array = isset($final_isr_array)?$final_isr_array:array();
       

    $cdataa = "SELECT id, name FROM user_category";
        $cquerya = mysqli_query($dbc,$cdataa);
         if($cquerya)
         {
             while($complaina = mysqli_fetch_assoc($cquerya))
             {
                 $compa['id'] = $complaina['id'];
                 $compa['name'] = $complaina['name'];
                 $final_compa_details[] = $compa;
             }
         }
        //This query is used to send top level category list
        $query_category = "SELECT id,name FROM catalog_2 ORDER BY id ASC";
        $run_category = mysqli_query($dbc, $query_category) or die(mysqli_error($dbc));
        if (mysqli_num_rows($run_category) > 0) {
            while ($category_fetch = mysqli_fetch_assoc($run_category)) {
                $category_info['id'] = $category_fetch['id'];
                $category_info['name'] = $category_fetch['name'];
                $final_category_details[] = $category_info;
            }
        }

        $essential[] = array("response" => "TRUE"
            , "user_id" => $person_id
            , "person_role" => $role_id
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
            , "product" => $final_catalog_product_details
            , "damage_product"=>$final_damage_product_details
            , "complaint"=>$final_comp_details
            , "user_category"=>$final_compa_details
            , "category" => $final_category_details
            , "outlets" => $final_outlet_details
            , "ownership_types" => $final_ownership_details
            , "field_experience" => $final_experience_details
            , "market_gift" => $final_retailer_gift
            , "travelling_modes" => $final_travel_deatails
            , "working_status" => $final_working_deatails
            , "isr_details" => $final_isr_array
            , "manual_attendance_usr" => $user_for_manual_attendance
            , "role" => $final_role_deatails
            , "tracking" => $stimedetails_info
            , "tracking_interval" => $time_info
            , "retailer_increment_id" => $retailer_increment_id
        );
    } //if(empty($row['last_mobile_access_on'])) end here
    else if ($row['sync_status'] == '1') { // updated data
        if (!empty($dealer)) { //
            $q = "SELECT id,name FROM dealer d  "
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
                    . "AND udr.user_id = '" . $row['id'] . "' GROUP BY locname ASC";

            $r = mysqli_query($dbc, $q);

            while ($rows = mysqli_fetch_assoc($r)) {
                $beat_info['lid'] = $rows['lid'];
                $beat_info['locname'] = $rows['locname'];
                $beat_info['dealer_id'] = $rows['dealer_id'];
                $final_beat_details[] = $beat_info;
            }
        }
        if (!empty($location)) { // here we used inner join form retailer location level attached
            $q = "SELECT l.name AS locname,l.id AS lid,dealer_id "
                    . "FROM user_dealer_retailer udr INNER "
                    . "JOIN dealer_location_rate_list USING(dealer_id) INNER "
                    . "JOIN location_$const_loc_level[retailer_level] AS l "
                    . "WHERE l.id IN ($location) "
                    . "AND udr.user_id = '" . $row['id'] . "' GROUP BY locname ASC";
            $r = mysqli_query($dbc, $q);
            while ($rows = mysqli_fetch_assoc($r)) {
                $dealer_location['lid'] = $rows['lid'];
                $dealer_location['locname'] = $rows['locname'];
                $dealer_location['dealer_id'] = $rows['dealer_id'];
                $final_dealer_location_details[] = $dealer_location;
            }
        }
        $essential[] = array("response" => "TRUE"
            , "user_id" => $person_id
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
            , "product" => $final_catalog_product_details
            , "catalog_id_fetch" => $catalog_id_fetch
            , "catalog_id_delete" => $catalog_id_delete
            , "category" => $final_category_details
            , "outlets" => $final_outlet_details
            , "ownership_types" => $final_ownership_details
            , "field_experience" => $final_experience_details
            , "market_gift" => $final_retailer_gift
            , "travelling_modes" => $final_travel_deatails
            , "working_status" => $final_working_deatails
            , "role" => $final_role_deatails
            , "tracking" => $stimedetails_info
            , "tracking_interval" => $time_info
            , "retailer_increment_id" => $retailer_increment_id
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
//        pre($final_array);
//        exit();
$data = json_encode($final_array);

$file = fopen("../../../webservices/new_signin_data/".$get_user_id.".php","w");

	fwrite($file,$data);
	$myfile = $get_user_id.".php";
	fclose($file);
	chmod("../../../webservices/new_signin_data/".$get_user_id.".php", 0777);
	
}

function signin_11Version_data_generate($get_user_id){

        global $dbc;

    $myobj = new mtp();
    /*$imei = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['imei'])));
    $uname = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['uname'])));
    $pass = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['pass'])));*/
    $id = mysqli_real_escape_string($dbc, trim(stripslashes($get_user_id)));
    $rest = substr($imei, -5) * 1; // multiplying by 1 for termination starting zero values

    $query_person_login = "SELECT * FROM `person_login` INNER JOIN person ON person_login.person_id = person.id AND person.id='$id' AND person_status = '1'  ORDER BY person_username ASC";
    //h1($query_person_login);
    $user_qry = mysqli_query($dbc, $query_person_login);
    //$user_res=  mysqli_fetch_assoc($user_qry);
    $company_id = 1;
    if ($user_qry && mysqli_num_rows($user_qry) > 0) {
    //$tracking = $myobj->tracking_mobile_data($filter="person_username = '$uname' AND person_password = '$pass' AND imei_number = '$imei' AND person_status = '1' ",  $records = '', $orderby='ORDER BY person_username ASC');
        $tracking_constant = array();
        $tracking = $myobj->get_constant_datas_list($filter = "", $records = '', $orderby = '');

        $tracking = $tracking[1];
        $tracking_constant[] = $tracking;
    //pre($tracking_constant);
        $tracking_interval = explode(',', $tracking['tracking_intervals']);

        $inc = 1;
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
        $final_dealer_details = array();
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
	$target_achieved = array();
	$isr_array = array();
        $person_info = array();
        $final_damage_product_details = array();
        $final_isr_array = array();
        $user_for_manual_attendance = array();
        $user_retrailer_incrementid = 0;
        $constant_values = "";
	

        $query_constant_level = "SELECT * FROM `_constant` WHERE company_id=1";
        $run_constant_level = mysqli_query($dbc, $query_constant_level);
        $dconstant_data = mysqli_fetch_assoc($run_constant_level);
        $dconstant_data;
        if ($dconstant_data['constant_status'] == 1) {
            //    $constant_data = $dconstant_data;
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
        $qoutlet = "SELECT id, outlet_type FROM _retailer_outlet_type";
        $routlet = mysqli_query($dbc, $qoutlet);
        if ($routlet) {
            while ($doutlet = mysqli_fetch_assoc($routlet)) {
                $outlet['id'] = $doutlet['id'];
                $outlet['outlet_type'] = $doutlet['outlet_type'];
                $final_outlet_details[] = $outlet;
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
        $role_id = $row['role_id'];
        $state_id = $row['state_id'];
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


    //here we get user person details
        $query_person = "SELECT p.id AS rId, CONCAT_WS(' ',first_name,middle_name,last_name) AS full_name,mobile,address,dob FROM person p INNER JOIN person_details pd ON pd.person_id = p.id WHERE p.id = $row[id]";
        $run_person = mysqli_query($dbc, $query_person);
        $fetch_person = mysqli_fetch_assoc($run_person);
        $id = $fetch_person['rId'];
        $person_info[] = $fetch_person;
        // user person details end here
        if ($row['sync_status'] == '0') { // here we get all data
            //This query is used to fetch dealer information
            $query_dealer = "SELECT d.id,d.name FROM dealer AS d INNER JOIN user_dealer_retailer AS udr ON udr.dealer_id = d.id AND udr.user_id = '$row[id]' GROUP BY id DESC"; //user_id 

            $run_dealer = mysqli_query($dbc, $query_dealer);
            if (mysqli_num_rows($run_dealer) > 0) {
                while ($dealer_fetch = mysqli_fetch_assoc($run_dealer)) {
                    $dealer_info['id'] = $dealer_fetch['id'];
                    $dealer_info['name'] = $dealer_fetch['name'];
                    //$dealer_info['ss_id'] = $dealer_fetch['csa_id'];
                    $dealer_array[] = $dealer_fetch['id'];
                    $final_dealer_details[] = $dealer_info;
                }
            }

            if (!empty($dealer_array)) {
                //This dealer information end here
                $dealerarray = implode(",", $dealer_array); // here we get all the dealer id
                //This query is used to fetch location of the dealer in other words we can say that here we find out beat
                $query_locality = "SELECT l.name AS locname,l.id AS lid,dealer_id "
                        . "FROM dealer_location_rate_list AS dl INNER "
                        . "JOIN location_$dconstant_data[dealer_level] AS l "
                        . "ON l.id = dl.location_id "
                        . "WHERE dl.dealer_id in ($dealerarray)"; //here we get beat 
                //echo $query_locality; exit();
                $run_locality = mysqli_query($dbc, $query_locality) or die(mysqli_error($dbc));
                if (mysqli_num_rows($run_locality) > 0) {
                    while ($locality_fetch = mysqli_fetch_assoc($run_locality)) {
                        $beat_info['id'] = $locality_fetch['lid'];
                        $beat_array[] = $locality_fetch['lid'];
                        $beat_info['name'] = $locality_fetch['locname'];
                        $beat_info['dealer_id'] = $locality_fetch['dealer_id'];
                        $final_beat_details[] = $beat_info;
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

                $rqlocation = mysqli_query($dbc, $querylocation) or die(mysqli_error($dbc));
                if (mysqli_num_rows($rqlocation) > 0) {
                    while ($dlocation = mysqli_fetch_assoc($rqlocation)) {
                        $dealer_location['dealer_id'] = $dlocation['dealer_id'];
                        $dealer_location['id'] = $dlocation['location'];
                        $reatiler_loc_array[] = $dlocation['location'];
                        $dealer_location['name'] = $dlocation['name'];
                        $final_dealer_location_details[] = $dealer_location;
                    }
                }
            } //if(!empty($beat_array))  end here
            //here some confusion and i need to ask sujeet sir..
            if (!empty($reatiler_loc_array))
                $reatiler_loc_array_str = implode(',', $reatiler_loc_array);
            else
                $reatiler_loc_array_str = '';
            $query_retailer_access_on = "SELECT r.id,r.name,location_id,rl.name AS loc_name,r.address FROM retailer r INNER JOIN user_dealer_retailer udr ON udr.retailer_id = r.id INNER JOIN location_$dconstant_data[retailer_level] AS rl ON rl.id = r.location_id WHERE user_id = '$row[id]' AND r.location_id IN ($reatiler_loc_array_str) GROUP BY id DESC"; //h1($query_retailer_access_on);

            $run_retailer = mysqli_query($dbc, $query_retailer_access_on);
            if (mysqli_num_rows($run_retailer) > 0) {
                while ($retailer_fetch = mysqli_fetch_assoc($run_retailer)) {
                    $retailer_info['id'] = $retailer_fetch['id'];
                    $retailer_info['name'] = $retailer_fetch['name'].' ['.$retailer_fetch['address'].']';
                    $retailer_info['location_id'] = $retailer_fetch['location_id'];
                    $retailer_info['loc_name'] = $retailer_fetch['loc_name'];
                    $final_retailer_details[] = $retailer_info;
                }
            }

            //This query is Used to send catalog Classification name list
            $final_product_classification_details=array();
            $query_classification = "SELECT * FROM `catalog_1`";
            $run_classifiction = mysqli_query($dbc,$query_classification);
            if(mysqli_num_rows($run_classifiction) >0){
                while($classification_fetch = mysqli_fetch_object($run_classifiction)){
                    $classification_info['id']=$classification_fetch->id;
                    $classification_info['name']=$classification_fetch->name;
                    $final_product_classification_details[]=$classification_info;
                }

            }

            //This query is Used to send catalog_product name list

               $query_catalog_product = "SELECT catalog_product.id,catalog_1.id as classification_id,catalog_1.name as classification_name,catalog_id,unit,catalog_product.name,cprl.base_price,cprl.rate as mrp,catalog_2.name as cname FROM catalog_product "
                        . " INNER JOIN catalog_product_rate_list cprl ON catalog_product.id = cprl.catalog_product_id"
                       . " INNER JOIN catalog_2 ON catalog_2.id=catalog_product.catalog_id INNER JOIN catalog_1 ON catalog_1.id=catalog_2.catalog_1_id  WHERE stateId='$state_id' ORDER BY name ASC";
                $run_catalog_product = mysqli_query($dbc, $query_catalog_product);
                if (mysqli_num_rows($run_catalog_product) > 0) {
                    while ($catalog_product_fetch = mysqli_fetch_assoc($run_catalog_product)) {
                        
                        $catalog_product_info['id'] = $catalog_product_fetch['id'];
                        $catalog_product_info['classification_id'] = $catalog_product_fetch['classification_id'];
                        $catalog_product_info['classification_name'] = $catalog_product_fetch['classification_name'];
                        $catalog_product_info['category'] = $catalog_product_fetch['catalog_id'];
                        $catalog_product_info['category_name'] = $catalog_product_fetch['cname'];
                        $catalog_product_info['name'] = $catalog_product_fetch['name'];
                        $catalog_product_info['base_price'] = $catalog_product_fetch['base_price'];
                        $catalog_product_info['mrp'] = $catalog_product_fetch['mrp'];
                        $catalog_product_info['unit'] = $catalog_product_fetch['unit'];
                        $final_catalog_product_details[] = $catalog_product_info;
                    }
                } // if(mysqli_num_rows($run_catalog_product)if(mysqli_num_rows($run_catalog_product)
            
                
            $query_damage_product = "SELECT id,`name` as catalog_product,base_price as rate,'1' as state_id FROM catalog_product";
                    
        $run_damage_product = mysqli_query($dbc, $query_damage_product) or die(mysqli_error($dbc));
            
            while($damage_product_fetch = mysqli_fetch_assoc($run_damage_product))
                    {
                       $damage_product_info['id'] = $damage_product_fetch['id'];
                       $damage_product_info['catalog_product'] =$damage_product_fetch['catalog_product'];

                       $damage_product_info['rate'] =$damage_product_fetch['rate'];
                       $final_damage_product_details[] = $damage_product_info;
                    }
        // h1($query_damage_product);
      
            $comp=array();
        $final_comp_details=array();
            $compa=array();
        $final_compa_details=array();
        $cdata = "SELECT id, name FROM complaint_type";
               //h1($cdata);
             $cquery = mysqli_query($dbc,$cdata);
             if($cquery)
             {
                 while($complain = mysqli_fetch_assoc($cquery))
                 {
                     $comp['id'] = $complain['id'];
                     $comp['name'] = $complain['name'];
                     $final_comp_details[] = $comp;
                 }
             }

         //final_isr_array   
             $idealer_data = "SELECT GROUP_CONCAT(DISTINCT dealer_id ORDER BY dealer_id ASC SEPARATOR ',') as dealer_id FROM user_dealer_retailer where user_id='$row[id]' ";
             // h1($idealer_data);die;
             $idlr_query = mysqli_query($dbc,$idealer_data);
             $idlr_row =mysqli_fetch_object($idlr_query);
            $idealer_id =$idlr_row->dealer_id;

         $isr_data = "SELECT rolename,person.id,CONCAT_WS(' ',first_name,middle_name,last_name) as name,dealer_id,dealer.name as dealer_name FROM person INNER JOIN _role ON _role.role_id=person.role_id INNER JOIN user_dealer_retailer udr ON udr.user_id=person.id INNER JOIN dealer ON udr.dealer_id=dealer.id where dealer_id IN($idealer_id) and (person.role_id='14' OR person.role_id='15') group by dealer_id,person.id";
           // h1($isr_data);
             $isr_query = mysqli_query($dbc,$isr_data);
             if($isr_query)
             {
                 while($isr = mysqli_fetch_assoc($isr_query))
                 {
                     $isr_detail['id'] = $isr['id'];
                     $isr_detail['isr_name'] = $isr['name'].'/'.$isr['rolename'];
                     $isr_detail['isr_dealer_id'] = $isr['dealer_id'];
                     $isr_detail['isr_dealer_name'] = $isr['dealer_name'];
                     $isr_array[] = $isr_detail;
                 }
                 //pre($final_isr_array);die;
             }

            //user_for_manual_attendance
             $user_for_manual_attendance = $myobj->recursiveall2_signin($row[id]);
            $user_for_manual_attendance= isset($_SESSION['juniordata'])?$_SESSION['juniordata']:array();
             
             
            $final_isr_array=(array_merge($isr_array,$user_for_manual_attendance));
            $final_isr_array = isset($final_isr_array)?$final_isr_array:array();
           

        $cdataa = "SELECT id, name FROM user_category";
            $cquerya = mysqli_query($dbc,$cdataa);
             if($cquerya)
             {
                 while($complaina = mysqli_fetch_assoc($cquerya))
                 {
                     $compa['id'] = $complaina['id'];
                     $compa['name'] = $complaina['name'];
                     $final_compa_details[] = $compa;
                 }
             }
            //This query is used to send top level category list
            $query_category = "SELECT id,name FROM catalog_2 ORDER BY id ASC";
            $run_category = mysqli_query($dbc, $query_category) or die(mysqli_error($dbc));
            if (mysqli_num_rows($run_category) > 0) {
                while ($category_fetch = mysqli_fetch_assoc($run_category)) {
                    $category_info['id'] = $category_fetch['id'];
                    $category_info['name'] = $category_fetch['name'];
                    $final_category_details[] = $category_info;
                }
            }

            $essential[] = array("response" => "TRUE"
                , "user_id" => $person_id
                , "person_role" => $role_id
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
                , "damage_product"=>$final_damage_product_details
                , "complaint"=>$final_comp_details
                , "user_category"=>$final_compa_details
                , "category" => $final_category_details
                , "outlets" => $final_outlet_details
                , "ownership_types" => $final_ownership_details
                , "field_experience" => $final_experience_details
                , "market_gift" => $final_retailer_gift
                , "travelling_modes" => $final_travel_deatails
                , "working_status" => $final_working_deatails
                , "isr_details" => $final_isr_array
                , "manual_attendance_usr" => $user_for_manual_attendance
                , "role" => $final_role_deatails
                , "tracking" => $stimedetails_info
                , "tracking_interval" => $time_info
                , "retailer_increment_id" => $retailer_increment_id
            );
        } //if(empty($row['last_mobile_access_on'])) end here
        else if ($row['sync_status'] == '1') { // updated data
            if (!empty($dealer)) { //
                $q = "SELECT id,name FROM dealer d  "
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
                        . "AND udr.user_id = '" . $row['id'] . "' GROUP BY locname ASC";

                $r = mysqli_query($dbc, $q);

                while ($rows = mysqli_fetch_assoc($r)) {
                    $beat_info['lid'] = $rows['lid'];
                    $beat_info['locname'] = $rows['locname'];
                    $beat_info['dealer_id'] = $rows['dealer_id'];
                    $final_beat_details[] = $beat_info;
                }
            }
            if (!empty($location)) { // here we used inner join form retailer location level attached
                $q = "SELECT l.name AS locname,l.id AS lid,dealer_id "
                        . "FROM user_dealer_retailer udr INNER "
                        . "JOIN dealer_location_rate_list USING(dealer_id) INNER "
                        . "JOIN location_$const_loc_level[retailer_level] AS l "
                        . "WHERE l.id IN ($location) "
                        . "AND udr.user_id = '" . $row['id'] . "' GROUP BY locname ASC";
                $r = mysqli_query($dbc, $q);
                while ($rows = mysqli_fetch_assoc($r)) {
                    $dealer_location['lid'] = $rows['lid'];
                    $dealer_location['locname'] = $rows['locname'];
                    $dealer_location['dealer_id'] = $rows['dealer_id'];
                    $final_dealer_location_details[] = $dealer_location;
                }
            }
            $essential[] = array("response" => "TRUE"
                , "user_id" => $person_id
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
                , "ownership_types" => $final_ownership_details
                , "field_experience" => $final_experience_details
                , "market_gift" => $final_retailer_gift
                , "travelling_modes" => $final_travel_deatails
                , "working_status" => $final_working_deatails
                , "role" => $final_role_deatails
                , "tracking" => $stimedetails_info
                , "tracking_interval" => $time_info
                , "retailer_increment_id" => $retailer_increment_id
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
    //        pre($final_array);
    //        exit();
    $data = json_encode($final_array);

    $file = fopen("../../../webservices/signin_11version_data/".$get_user_id.".php","w");

        fwrite($file,$data);
	$myfile = $get_user_id.".php";
	
        fclose($file);
	chmod("../../../webservices/signin_11version_data/".$get_user_id.".php", 0777);
	

}

function signin_12Version_data_generate($get_user_id){

        global $dbc;

    $myobj = new mtp();
    /*$imei = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['imei'])));
    $uname = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['uname'])));
    $pass = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['pass'])));*/
    $id = mysqli_real_escape_string($dbc, trim(stripslashes($get_user_id)));
    $rest = substr($imei, -5) * 1; // multiplying by 1 for termination starting zero values
/////////////////////ANK////////////////////
    $query_person_login = "SELECT *,person.mobile,CONCAT_WS(' ',first_name,middle_name,last_name) as full_person_name FROM `person_login` INNER JOIN person ON person_login.person_id = person.id AND person.id='$id' AND person_status = '1'  ORDER BY person_username ASC";
    //h1($query_person_login);
    $user_qry = mysqli_query($dbc, $query_person_login);
    //$user_res=  mysqli_fetch_assoc($user_qry);
    $company_id = 1;
    if ($user_qry && mysqli_num_rows($user_qry) > 0) {
    //$tracking = $myobj->tracking_mobile_data($filter="person_username = '$uname' AND person_password = '$pass' AND imei_number = '$imei' AND person_status = '1' ",  $records = '', $orderby='ORDER BY person_username ASC');
        $tracking_constant = array();
        $tracking = $myobj->get_constant_datas_list($filter = "", $records = '', $orderby = '');

        $tracking = $tracking[1];
        $tracking_constant[] = $tracking;
    //pre($tracking_constant);
        $tracking_interval = explode(',', $tracking['tracking_intervals']);

        $inc = 1;
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
        $final_dealer_details = array();
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
        $gift= array();
        $person_info = array();
        $final_damage_product_details = array();
        $final_isr_array = array();
	$isr_array = array();
        $user_for_manual_attendance = array();
        $user_retrailer_incrementid = 0;
        $constant_values = "";

        $query_constant_level = "SELECT * FROM `_constant` WHERE company_id=1";
        $run_constant_level = mysqli_query($dbc, $query_constant_level);
        $dconstant_data = mysqli_fetch_assoc($run_constant_level);
        $dconstant_data;
        if ($dconstant_data['constant_status'] == 1) {
            //    $constant_data = $dconstant_data;
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
        $qoutlet = "SELECT id, outlet_type FROM _retailer_outlet_type";
        $routlet = mysqli_query($dbc, $qoutlet);
        if ($routlet) {
            while ($doutlet = mysqli_fetch_assoc($routlet)) {
                $outlet['id'] = $doutlet['id'];
                $outlet['outlet_type'] = $doutlet['outlet_type'];
                $final_outlet_details[] = $outlet;
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
        $person_email = $row['email'];
	$emp_code = $row['emp_code'];
        $pdd = mysqli_query($dbc,"SELECT dob,state_id FROM `person_details` WHERE `person_id` = '$person_id'");
        $ro = mysqli_fetch_assoc($pdd);
        $dob = $ro['dob'];
	$state_id = $ro['state_id'];
        
        $role_id = $row['role_id'];
        $full_person_name = $row['full_person_name'];
        $person_contact = $row['mobile'];
        $state_id = $row['state_id'];
        //$dob = $row['dob'];
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
        $final_dealer_details1 = array();

    //here we get user person details
        $query_person = "SELECT p.id AS rId, CONCAT_WS(' ',first_name,middle_name,last_name) AS full_name,mobile,address,dob FROM person p INNER JOIN person_details pd ON pd.person_id = p.id WHERE p.id = $row[id]";
        $run_person = mysqli_query($dbc, $query_person);
        $fetch_person = mysqli_fetch_assoc($run_person);
        $id = $fetch_person['rId'];
        $person_info[] = $fetch_person;
        // user person details end here
        if ($row['sync_status'] == '0') { // here we get all data
            //This query is used to fetch dealer information
if($role_id!=5)
{
            $query_dealer = "SELECT d.id,d.name,csa_id FROM dealer AS d INNER JOIN user_dealer_retailer AS udr ON udr.dealer_id = d.id AND udr.user_id = '$row[id]' GROUP BY id DESC"; //user_id 

            $run_dealer = mysqli_query($dbc, $query_dealer);
            if (mysqli_num_rows($run_dealer) > 0) {
                while ($dealer_fetch = mysqli_fetch_assoc($run_dealer)) {
                    $dealer_info['id'] = $dealer_fetch['id'];
                    $dealer_info['name'] = $dealer_fetch['name'];
              //      $dealer_info['ss_id'] = $dealer_fetch['csa_id'];
		//	$cid = $dealer_fetch['csa_id'];
		//$ss = mysqli_query($dbc,"SELECT csa_name FROM `csa` WHERE c_id=$cid");
		//$ss_row = mysqli_fetch_assoc($ss);
		//$dealer_info['ss_name']=$ss_row['csa_name'];
                    $dealer_array[] = $dealer_fetch['id'];
                    $final_dealer_details[] = $dealer_info;
                }
            }
}
if($role_id==5)
{
$final_person_details1 = array();
$query_dealer = "SELECT d.id,d.name,csa_id FROM dealer AS d INNER JOIN user_dealer_retailer AS udr ON udr.dealer_id = d.id AND udr.user_id = '$row[id]' GROUP BY id DESC"; //user_id 

            $run_dealer = mysqli_query($dbc, $query_dealer);
            if (mysqli_num_rows($run_dealer) > 0) {
                while ($dealer_fetch = mysqli_fetch_assoc($run_dealer)) {
                    $dealer_info1['id'] = $dealer_fetch['id'];
                    $dealer_info1['name'] = $dealer_fetch['name'];
                    $dealer_info1['ss_id'] = $dealer_fetch['csa_id'];
			$cid = $dealer_fetch['csa_id'];
		$ss = mysqli_query($dbc,"SELECT csa_name FROM `csa` WHERE c_id=$cid");
		$ss_row = mysqli_fetch_assoc($ss);
		$dealer_info1['ss_name']=$ss_row['csa_name'];
                    $dealer_array[] = $dealer_fetch['id'];
                    $final_dealer_details1[] = $dealer_info1;
                }
            }
 /////////////////////NUMBER OF SALE////////////////////////////////
                $query_cs = "SELECT id FROM  `user_sales_order` WHERE dealer_id='$dealer_info1[id]'";
                 $run_cs = mysqli_query($dbc, $query_cs) or die(mysqli_error($dbc));
		$sale = mysqli_num_rows($run_cs);
		$query_ch = "SELECT id  FROM  `challan_order` WHERE ch_dealer_id='$dealer_info1[id]'";
                 $run_ch = mysqli_query($dbc, $query_ch) or die(mysqli_error($dbc));
		$chal = mysqli_num_rows($run_ch);
              // echo $dealer_info1['id']." ".$sale."  ".$chal;
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

   
/////////////////////DEALER TARGET VALUE////////////////////////////////
 		$dealer_target = array();
               $m = date('m');
            // h1($m);
              $query_tar = "SELECT may FROM `dealer_target` WHERE dealer_id='$dealer_info1[id]'";
		//h1($query_tar);
               $run_tar = mysqli_query($dbc, $query_tar) or die(mysqli_error($dbc));
            if (mysqli_num_rows($run_tar) > 0) {
                while ($sc_tar = mysqli_fetch_assoc($run_tar)){
                    $tar_info['target'] = $sc_tar['may'];
	            $dealer_target[] = $tar_info;
                }
            } 
         ////////////////////////////????ACHIEVED///////////////////////////////////
         $ch = "SELECT SUM(taxable_amt) as achived,date_format(`ch_date`,'%Y-%m-%d') as ch_date FROM `challan_order` as co INNER JOIN challan_order_details as cod ON cod.ch_id=co.id WHERE ch_dealer_id='$dealer_info1[id]' AND date_format(`ch_date`,'%m')='$m' GROUP BY ch_date ASC"; 
	//h1($ch);
         $run_ac = mysqli_query($dbc, $ch) or die(mysqli_error($dbc));
            if (mysqli_num_rows($run_ac) > 0) {
		$ar_info = array();
                while ($sc_ac = mysqli_fetch_assoc($run_ac)){
		$ar_inf['achived'] = $sc_ac['achived'];
		$ar_inf['date'] = $sc_ac['ch_date'];
                 $ar_info[] = $ar_inf;
                   }
            }
			
			 ////////////////////////////RETAILER DEALER///////////////////////////////////
       //  $ch = "SELECT SUM(taxable_amt) as achived,date_format(`ch_date`,'%Y-%m-%d') as ch_date FROM `challan_order` as co INNER JOIN challan_order_details as cod ON cod.ch_id=co.id WHERE ch_dealer_id='$dealer_info1[id]' AND date_format(`ch_date`,'%m')='$m' GROUP BY ch_date ASC"; 
	//h1($ch);
	$chre = "SELECT r.id as id,r.email as email1,r.landline as contact,r.contact_per_name as contact_person,r.other_numbers,r.name as name,
	location_id,rl.name AS loc_name
	,r.address as address,r.tin_no as tin,lat_long FROM retailer r INNER JOIN user_dealer_retailer udr ON udr.retailer_id = r.id 
	INNER JOIN location_$dconstant_data[retailer_level] AS rl ON rl.id = r.location_id WHERE r.dealer_id='$dealer_info1[id]' GROUP BY r.id";
//h1($chre);
         $run_chre = mysqli_query($dbc, $chre);
            if (mysqli_num_rows($run_chre) > 0) {
		$run_chre_info = array();
		$run_chre_dealer = array();
                while ($sc_ch = mysqli_fetch_assoc($run_chre)){
					$ll = $sc_ch['lat_long'];
					$latlng = explode(",",$ll);
		$run_chre_dealer['id'] = $sc_ch['id'];
		$run_chre_dealer['lat'] = $latlng[0];
		$run_chre_dealer['lng'] = $latlng[1];
		$run_chre_dealer['name'] = $sc_ch['name'];
		$run_chre_dealer['location_id'] = $sc_ch['location_id'];
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
            
}

            if (!empty($dealer_array)) {
                //This dealer information end here
                $dealerarray = implode(",", $dealer_array); // here we get all the dealer id
                //This query is used to fetch location of the dealer in other words we can say that here we find out beat
                $query_locality = "SELECT l.name AS locname,l.id AS lid,dealer_id "
                        . "FROM dealer_location_rate_list AS dl INNER "
                        . "JOIN location_$dconstant_data[dealer_level] AS l "
                        . "ON l.id = dl.location_id "
                        . "WHERE dl.dealer_id in ($dealerarray) group by l.id"; //here we get beat 
                //echo $query_locality; exit();
                $run_locality = mysqli_query($dbc, $query_locality) or die(mysqli_error($dbc));
                if (mysqli_num_rows($run_locality) > 0) {
                    while ($locality_fetch = mysqli_fetch_assoc($run_locality)) {
                        $beat_info['id'] = $locality_fetch['lid'];
                        $beat_array[] = $locality_fetch['lid'];
                        $beat_info['name'] = $locality_fetch['locname'];
                        $beat_info['dealer_id'] = $locality_fetch['dealer_id'];
                        $final_beat_details[] = $beat_info;
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

                $rqlocation = mysqli_query($dbc, $querylocation) or die(mysqli_error($dbc));
                if (mysqli_num_rows($rqlocation) > 0) {
                    while ($dlocation = mysqli_fetch_assoc($rqlocation)) {
                        $dealer_location['dealer_id'] = $dlocation['dealer_id'];
                        $dealer_location['id'] = $dlocation['location'];
                        $reatiler_loc_array[] = $dlocation['location'];
                        $dealer_location['name'] = $dlocation['name'];
                        $final_dealer_location_details[] = $dealer_location;
                    }
                }
            } //if(!empty($beat_array))  end here
            //here some confusion and i need to ask sujeet sir..
            if (!empty($reatiler_loc_array))
                $reatiler_loc_array_str = implode(',', $reatiler_loc_array);
            else
                $reatiler_loc_array_str = '';
            $query_retailer_access_on = "SELECT r.id,r.email as email1,r.landline,r.contact_per_name,r.other_numbers,r.name,location_id,rl.name AS loc_name,r.address,r.tin_no as tin,lat_long FROM retailer r INNER JOIN user_dealer_retailer udr ON udr.retailer_id = r.id INNER JOIN location_$dconstant_data[retailer_level] AS rl ON rl.id = r.location_id WHERE user_id = '$row[id]' AND r.location_id IN ($reatiler_loc_array_str) GROUP BY id DESC"; 
//h1($query_retailer_access_on);

            $run_retailer = mysqli_query($dbc, $query_retailer_access_on);
            if (mysqli_num_rows($run_retailer) > 0) {
                while ($retailer_fetch = mysqli_fetch_assoc($run_retailer)) {
                    $retailer_info['id'] = $retailer_fetch['id'];
                    $lat_lng = $retailer_fetch['lat_long'];

           $rso = mysqli_query($dbc,"SELECT sum(total_amount) as paid FROM payment_collection WHERE retailer_id = '$retailer_info[id]'");
		$row_rso = mysqli_fetch_assoc($rso);
		$paid = $row_rso['paid'];
		//$date_pay = $row_rso['date'];

      $ccso = mysqli_query($dbc,"SELECT SUM(`amount`) AS ch_amt FROM  `challan_order` WHERE ch_retailer_id = '$retailer_info[id]'");

		$row_cso = mysqli_fetch_assoc($ccso);
		$ch_amt = $row_cso['ch_amt'];
		$outstanding = $ch_amt-$paid;
          
        $rso1 = mysqli_query($dbc,"SELECT total_amount FROM payment_collection WHERE retailer_id = '$retailer_info[id]' Order By pay_date_time limit 0,1");
		$row_rso1 = mysqli_fetch_assoc($rso1);
		$last = $row_rso1['total_amount'];
		
          //$rso = "SELECT `lat_long` FROM `retailer` WHERE `id` = '$retailer_fetch[id]'"; 
 //h1($rso);
          // $rsol = mysqli_query($dbc,$rso);
          // while($row = mysqli_fetch_assoc($rsol))
          // {
             //  $lat_lng = $row['lat_long'];  
          // }
           $ll = explode(",",$lat_lng);
           $lat = $ll[0];
           $lng = $ll[1];
                    $retailer_info['lat'] = $lat;
                    $retailer_info['lng'] = $lng;
                    $retailer_info['name'] = $retailer_fetch['name'];
                    $retailer_info['location_id'] = $retailer_fetch['location_id'];
                    $retailer_info['loc_name'] = $retailer_fetch['loc_name'];
                    $retailer_info['address'] = $retailer_fetch['address'];
                    $retailer_info['email'] = $retailer_fetch['email1'];
                    $retailer_info['achieved'] = $ch_amt;   
		    $retailer_info['outstanding'] = $outstanding;
			if(!empty($last))
                    $retailer_info['last_amt'] = $last;
			else
			 $retailer_info['last_amt'] = 0;
			//if(!empty($date_pay))
           ///      $retailer_info['last_date'] = $date_pay;
			//else
			$retailer_info['last_date'] = "No Date";

                    $retailer_info['contact_no'] = $retailer_fetch['landline'].','.$retailer_fetch['other_numbers'];
                    $retailer_info['contact_person'] = $retailer_fetch['contact_per_name'];
		    $retailer_info['tin'] = $retailer_fetch['tin'];
                    $final_retailer_details[] = $retailer_info;
                }
            }

            //This query is Used to send catalog Classification name list
            $final_product_classification_details=array();
            $query_classification = "SELECT * FROM `catalog_1`";
            $run_classifiction = mysqli_query($dbc,$query_classification);
            if(mysqli_num_rows($run_classifiction) >0){
                while($classification_fetch = mysqli_fetch_object($run_classifiction)){
                    $classification_info['id']=$classification_fetch->id;
                    $classification_info['name']=$classification_fetch->name;
                    $final_product_classification_details[]=$classification_info;
                }

            }

            //This query is Used to send catalog_product name list

               $query_catalog_product = "SELECT catalog_product.id,catalog_1.id as classification_id,catalog_1.name as classification_name,catalog_id,unit,catalog_product.name,cprl.base_price,cprl.rate as mrp,catalog_2.name as cname FROM catalog_product "
                        . " INNER JOIN catalog_product_rate_list cprl ON catalog_product.id = cprl.catalog_product_id"
                       . " INNER JOIN catalog_2 ON catalog_2.id=catalog_product.catalog_id INNER JOIN catalog_1 ON catalog_1.id=catalog_2.catalog_1_id  WHERE stateId='$state_id' ORDER BY name ASC";
                $run_catalog_product = mysqli_query($dbc, $query_catalog_product);
                if (mysqli_num_rows($run_catalog_product) > 0) {
                    while ($catalog_product_fetch = mysqli_fetch_assoc($run_catalog_product)) {
                        
                        $i = '0';
                        $pid = $catalog_product_fetch['id'];
                    $focus = mysqli_query($dbc,"select `product_id` from `focus` where `product_id`=$pid");  
                         if(mysqli_num_rows($focus) >0){
                             $i = '1';
                         }
	//////////////////////////////////TAX////////ANK////////////////////////////////
		 $taxx = mysqli_query($dbc,"SELECT tax FROM `catalog_product_rate_list` where `catalog_product_id`=$pid AND `stateId`=$state_id");
			$row_tax = mysqli_fetch_assoc($taxx);
			
/////////////////////////////////////////////////////////////////////////////////////////////////////////  
                        $catalog_product_info['id'] = $catalog_product_fetch['id'];
                        $catalog_product_info['classification_id'] = $catalog_product_fetch['classification_id'];
                        $catalog_product_info['classification_name'] = $catalog_product_fetch['classification_name'];
                        $catalog_product_info['category'] = $catalog_product_fetch['catalog_id'];
                        $catalog_product_info['category_name'] = $catalog_product_fetch['cname'];
                        $catalog_product_info['name'] = $catalog_product_fetch['name'];
                        $catalog_product_info['base_price'] = $catalog_product_fetch['base_price'];
                        $catalog_product_info['mrp'] = $catalog_product_fetch['mrp'];
                        $catalog_product_info['unit'] = $catalog_product_fetch['unit'];
                        $catalog_product_info['focus'] = $i;
			$catalog_product_info['tax'] = $row_tax['tax'];
			//$catalog_product_info['state'] = $state_id;
                        $final_catalog_product_details[] = $catalog_product_info;
			
                    }
                } // if(mysqli_num_rows($run_catalog_product)if(mysqli_num_rows($run_catalog_product)
            
                
            $query_damage_product = "SELECT id,`name` as catalog_product,base_price as rate,'1' as state_id FROM catalog_product";
                    
        $run_damage_product = mysqli_query($dbc, $query_damage_product) or die(mysqli_error($dbc));
            
            while($damage_product_fetch = mysqli_fetch_assoc($run_damage_product))
                    {
                       $damage_product_info['id'] = $damage_product_fetch['id'];
                       $damage_product_info['catalog_product'] =$damage_product_fetch['catalog_product'];

                       $damage_product_info['rate'] =$damage_product_fetch['rate'];
                       $final_damage_product_details[] = $damage_product_info;
                    }
        // h1($query_damage_product);
      
            $comp=array();
        $final_comp_details=array();
            $compa=array();
        $final_compa_details=array();
        $cdata = "SELECT id, name FROM complaint_type";
               //h1($cdata);
             $cquery = mysqli_query($dbc,$cdata);
             if($cquery)
             {
                 while($complain = mysqli_fetch_assoc($cquery))
                 {
                     $comp['id'] = $complain['id'];
                     $comp['name'] = $complain['name'];
                     $final_comp_details[] = $comp;
                 }
             }

         //final_isr_array   
             $idealer_data = "SELECT GROUP_CONCAT(DISTINCT dealer_id ORDER BY dealer_id ASC SEPARATOR ',') as dealer_id FROM user_dealer_retailer where user_id='$person_id' ";
            //  h1($idealer_data);
             $idlr_query = mysqli_query($dbc,$idealer_data);
             $idlr_row =mysqli_fetch_object($idlr_query);
            $idealer_id =$idlr_row->dealer_id;

        // $isr_data = "SELECT rolename,person.id,CONCAT_WS(' ',first_name,middle_name,last_name) as name,dealer_id,dealer.name as dealer_name FROM person INNER JOIN _role ON _role.role_id=person.role_id INNER JOIN user_dealer_retailer udr ON udr.user_id=person.id INNER JOIN dealer ON udr.dealer_id=dealer.id where dealer_id IN($idealer_id) and (person.role_id='14' OR person.role_id='15') group by dealer_id,person.id";

 $isr_data = "SELECT rolename,person.id,CONCAT_WS(' ',first_name,middle_name,last_name) as name,dealer_id,dealer.name as dealer_name FROM person INNER JOIN _role ON _role.role_id=person.role_id INNER JOIN user_dealer_retailer udr ON udr.user_id=person.id INNER JOIN dealer ON udr.dealer_id=dealer.id where person_id_senior =$person_id and (person.role_id='14' OR person.role_id='15') group by dealer_id,person.id";
           // h1($isr_data);
             $isr_query = mysqli_query($dbc,$isr_data);
             if($isr_query)
             {
                 while($isr = mysqli_fetch_assoc($isr_query))
                 {
                     $isr_detail['id'] = $isr['id'];
                     $isr_detail['isr_name'] = $isr['name'].'/'.$isr['rolename'];
                     $isr_detail['isr_dealer_id'] = $isr['dealer_id'];
                     $isr_detail['isr_dealer_name'] = $isr['dealer_name'];
                     $isr_array[] = $isr_detail;
                 }
                 //pre($final_isr_array);die;
             }

            //user_for_manual_attendance
             $user_for_manual_attendance = $myobj->recursiveall2_signin($row[id]);
            $user_for_manual_attendance= isset($_SESSION['juniordata'])?$_SESSION['juniordata']:array();
             
             
            $final_isr_array=(array_merge($isr_array,$user_for_manual_attendance));
            $final_isr_array = isset($final_isr_array)?$final_isr_array:array();
           

        $cdataa = "SELECT id, name FROM user_category";
            $cquerya = mysqli_query($dbc,$cdataa);
             if($cquerya)
             {
                 while($complaina = mysqli_fetch_assoc($cquerya))
                 {
                     $compa['id'] = $complaina['id'];
                     $compa['name'] = $complaina['name'];
                     $final_compa_details[] = $compa;
                 }
             }
            //This query is used to send top level category list
            $query_category = "SELECT id,name FROM catalog_2 ORDER BY id ASC";
            $run_category = mysqli_query($dbc, $query_category) or die(mysqli_error($dbc));
            if (mysqli_num_rows($run_category) > 0) {
                while ($category_fetch = mysqli_fetch_assoc($run_category)) {
                    $category_info['id'] = $category_fetch['id'];
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
                    $leave[] = $l_info;
                }
            }
        //////////////////////////////////////////////////////////////
          /////////////////////Stock////////////////////////////////
                $query_s = "SELECT product_id,rate,dealer_id,remaining FROM `stock` ORDER BY id ASC";
            $run_s = mysqli_query($dbc, $query_s) or die(mysqli_error($dbc));
            if (mysqli_num_rows($run_s) > 0) {
                while ($s_fetch = mysqli_fetch_assoc($run_s)) {
                    $s_info['product_id'] = $s_fetch['product_id'];
                 //   $s_info['rate'] = $s_fetch['rate'];
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
                while ($g_fetch = mysqli_fetch_assoc($run_g)){
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
       $q= "SELECT SUM(rate*(quantity+scheme_qty)) as achieved FROM `user_primary_sales_order` upso INNER JOIN user_primary_sales_order_details upsod ON upsod.order_id = upso.order_id where is_claim=0 AND action=1 AND ch_date >='$ta_info[start_date]' AND ch_date<='$ta_info[end_date]'";
       // h1($q);
        $r = mysqli_query($dbc,$q);
        $row = mysqli_fetch_assoc($r);
        $ta_info['achieved'] = $row['achieved'];
                //echo $ta_info['achieved'];    
         $target_achieved[] = $ta_info;
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
          //      $comp = "SELECT * FROM `complaint` where user_id = $id ORDER BY id ASC";
         //  $run_comp = mysqli_query($dbc, $comp) or die(mysqli_error($dbc));
         //   if (mysqli_num_rows($run_comp) > 0) {
            ///    while ($comp_fetch = mysqli_fetch_assoc($run_comp)) {
                //    $comp_info['complaint_id'] = $comp_fetch['complaint_id'];
                //    $ctype = $comp_fetch['complaint_type'];
              //      $cdata = "SELECT id, name FROM complaint_type where `id`=$ctype";
 		$cdata = "SELECT id, name FROM complaint_type";
             $cquery = mysqli_query($dbc,$cdata);
              while($complain = mysqli_fetch_assoc($cquery))
                 {
		   $comp_info['complaint_id'] = $complain['id'];
                    $comp_info['complaint_type'] = $complain['name'];
		    $comp_info['action'] = '1';
                    $complaint[] = $comp_info;
                 }
            //     $comp_info['action'] = $comp_fetch['action'];
	
            //    }
           // }


             /////////////////////MTP////////////////////////////////
            $cdate = date('Y-m-d');
                $query_mtp = "SELECT total_sales,working_date,locations FROM `monthly_tour_program` WHERE date_format(`working_date`,'%Y-%m')= date_format('$cdate','%Y-%m') AND person_id='$person_id' ORDER BY id ASC";
            $run_mtp = mysqli_query($dbc, $query_mtp) or die(mysqli_error($dbc));
            if (mysqli_num_rows($run_mtp) > 0) {
                while ($sc_mtp = mysqli_fetch_assoc($run_mtp)) {
                    $mtp_info['total_sale'] = $sc_mtp['total_sales'];
                    $mtp_info['date'] = $sc_mtp['working_date'];
                    $loc_mt = $sc_mtp['locations'];
                   $loc = mysqli_query($dbc, "SELECT name FROM `location_5` WHERE `id` = $loc_mt");
                       $loc_row = mysqli_fetch_assoc($loc);
                       $mtp_info['today'] = $loc_row['name'];
			$mtp_info['today_id'] = $sc_mtp['locations'];
                    $mtp[] = $mtp_info;
                }
            }
            /////////////////////SCHEME BUILT VALUE////////////////////////////////
            $sosdate = date('Y-m-d');
                $query_sos = "SELECT sos.scheme_id,scheme_name,start_date,end_date,value,value_to,scheme_gift FROM `scheme_on_sale` sos INNER JOIN scheme_on_sale_details sosd ON sos.`scheme_id`=sosd.scheme_id WHERE '$sosdate' BETWEEN `start_date` AND `end_date` AND intype='1'";
            $run_sos = mysqli_query($dbc, $query_sos) or die(mysqli_error($dbc));
            if (mysqli_num_rows($run_sos) > 0) {
                while ($sc_sos = mysqli_fetch_assoc($run_sos)){
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
            if($row['sync_status']==null && $row['sync_status']==0)
            {
                $row['sync_status'] = '0';
            }
        ///////////////////// FOR DEALER USER///////////////////
if($role_id==5){            
$essential[] = array("response" => "TRUE"
                , "user_id" => $person_id
                , "full_person_name"=> $full_person_name 
                , "person_contact"=> $person_contact
                , "person_role" => $role_id
		, "dob" => $dob
                , "email"=>$person_email
                , "sync_status" => $row['sync_status']
		, "target" => $dealer_target
		, "achieved" => $ar_info
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
                , "product_id_fetch" => $product_id_fetch   
                , "product_id_delete" => $product_id_delete
		, "person" => $final_person_details1
                , "product_classification" => $final_product_classification_details
                , "product" => $final_catalog_product_details
                , "damage_product"=>$final_damage_product_details
                , "complaint"=>$final_comp_details
                , "user_category"=>$final_compa_details
                , "category" => $final_category_details
                , "outlets" => $final_outlet_details
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
                , "target_achieved" => $target_achieved
                , "complaint" => $complaint    
                , "gift" => $gift
                , "retailer_increment_id" => $retailer_increment_id
               
            );
}
else{
$essential[] = array("response" => "TRUE"
                , "user_id" => $person_id
                , "full_person_name"=> $full_person_name 
                , "person_contact"=> $person_contact
                , "person_role" => $role_id
                , "dob" => $emp_code
                , "email"=>$person_email
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
                , "damage_product"=>$final_damage_product_details
                , "complaint"=>$final_comp_details
                , "user_category"=>$final_compa_details
                , "category" => $final_category_details
                , "outlets" => $final_outlet_details
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
               
                , "complaint" => $complaint    
                , "gift" => $gift
                , "retailer_increment_id" => $retailer_increment_id
               
            );
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        } //if(empty($row['last_mobile_access_on'])) end here
        else if ($row['sync_status'] == '1') { // updated data
            if (!empty($dealer)) { //
                $q = "SELECT id,name FROM dealer d  "
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
                        . "AND udr.user_id = '" . $row['id'] . "' GROUP BY locname ASC";

                $r = mysqli_query($dbc, $q);

                while ($rows = mysqli_fetch_assoc($r)) {
                    $beat_info['lid'] = $rows['lid'];
                    $beat_info['locname'] = $rows['locname'];
                    $beat_info['dealer_id'] = $rows['dealer_id'];
                    $final_beat_details[] = $beat_info;
                }
            }
            if (!empty($location)) { // here we used inner join form retailer location level attached
                $q = "SELECT l.name AS locname,l.id AS lid,dealer_id "
                        . "FROM user_dealer_retailer udr INNER "
                        . "JOIN dealer_location_rate_list USING(dealer_id) INNER "
                        . "JOIN location_$const_loc_level[retailer_level] AS l "
                        . "WHERE l.id IN ($location) "
                        . "AND udr.user_id = '" . $row['id'] . "' GROUP BY locname ASC";
                $r = mysqli_query($dbc, $q);
                while ($rows = mysqli_fetch_assoc($r)) {
                    $dealer_location['lid'] = $rows['lid'];
                    $dealer_location['locname'] = $rows['locname'];
                    $dealer_location['dealer_id'] = $rows['dealer_id'];
                    $final_dealer_location_details[] = $dealer_location;
                }
            }
            $essential[] = array("response" => "TRUE"
                , "user_id" => $person_id
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
                , "ownership_types" => $final_ownership_details
                , "field_experience" => $final_experience_details
                , "market_gift" => $final_retailer_gift
                , "travelling_modes" => $final_travel_deatails
                , "working_status" => $final_working_deatails
                , "role" => $final_role_deatails
                , "tracking" => $stimedetails_info
                , "tracking_interval" => $time_info
                , "retailer_increment_id" => $retailer_increment_id
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
    //        pre($final_array);
    //        exit();
    $data = json_encode($final_array);

    $file = fopen("../../../webservices/signin_12version_data/".$get_user_id.".php","w");

        fwrite($file,$data);
	$myfile = $get_user_id.".php";
        fclose($file);
	chmod("../../../webservices/signin_12version_data/".$get_user_id.".php", 0777);
	
}

 function get_parent_child($parent_id) {
        global $dbc;
        $q = "SELECT name FROM _working_status WHERE id = $parent_id";
        $r = mysqli_query($dbc, $q);
        if ($r) {
            $row = mysqli_fetch_assoc($r);
            return $row['name'];
        }
    }
?>