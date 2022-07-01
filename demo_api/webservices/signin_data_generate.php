<?php

// alter query 
// ALTER TABLE `person_login` ADD `beat_id_fetch` INT NULL DEFAULT NULL AFTER `dealer_id_delete`, ADD `beat_id_delete` INT NULL DEFAULT NULL AFTER `beat_id_fetch`;
// Test URL
//http://localhost/msell/webservices/signin.php?imei=123456&uname=anil123&pass=anil123

require_once('../admin/functions/common_function.php');
require_once('../admin/include/conectdb.php');
require_once('../admin/include/config.inc.php');
require_once('../admin/include/my-functions.php');

$myobj = new mtp();
/*$imei = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['imei'])));
$uname = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['uname'])));
$pass = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['pass'])));*/
$id = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['id'])));
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

    function get_parent_child($parent_id) {
        global $dbc;
        $q = "SELECT name FROM _working_status WHERE id = $parent_id";
        $r = mysqli_query($dbc, $q);
        if ($r) {
            $row = mysqli_fetch_assoc($r);
            return $row['name'];
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
$data = json_encode($final_array);
echo $data;
//pre($final_array);
?>
