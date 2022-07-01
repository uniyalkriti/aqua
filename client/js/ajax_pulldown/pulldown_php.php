<?php
// This function will prepare the ajax response text which will be send to ajax call ends here
@session_start();
ob_start();
if(isset($_GET['pid']) && !empty($_GET['pid']))
{
	$id = $_GET['pid'];
	$wcase = $_GET['wcase'];
	//connect to the database
        require_once('../../include/config.inc.php');
        require_once(BASE_URI_ROOT.ADMINFOLDER.MSYM.'include'.MSYM.'my-functions.php');
        //include('../../include/my-functions.php');	
        //require_once(BASE_URI.'include'.MSYM.'my-functions.php');
        $setvalidator = false;
	$str = '';
	switch($wcase)
	{
		case 'get_senior_id':
		{
			$q = "SELECT id, CONCAT_WS(' ',first_name,middle_name,last_name) AS name FROM person WHERE role_id = '$id'";
			break;
		}
                
        case 'get_product_mrp':
		{
   //                      $product_id = $id;
			// $q = ' SELECT retailer_rate as rate,retailer_rate as rate from product_rate_list cp INNER JOIN user_primary_sales_order_details upsd ON cp.id = upsd.product_id INNER JOIN user_primary_sales_order ups USING(order_id) where cp.company_id =1 AND dealer_id = '.$_SESSION[SESS.'data']['dealer_id'].' AND upsd.product_id = '.$product_id.' Group by rate';
   //                  //    echo 'FALSE<$>'.$q;
			// break;
        $state= $_SESSION[SESS . 'data']['state_id'];
        $product_id = $id;
        $q= 'SELECT mrp,mrp from product_rate_list WHERE product_id = '.$product_id.' AND state_id='.$state.'';
        
            /*$q = "SELECT r.mrp as rate FROM product_rate_list r JOIN stock s ON r.product_id=s.product_id WHERE r.company_id = 1 AND s.dealer_id = '".$_SESSION[SESS.'data']['dealer_id']."' AND s.product_id='$product_id' group by s.rate";*/
           //echo 'FALSE<$>'.$q;
            break;
		}
                
//                  case 'get_product_hsn':
//		{
//                        $product_id = $id;
//			$q = 'SELECT hsn_code,hsn_code from catalog_product  where id = '.$product_id.'';
//
//                    break;
//		}
              
                
                case 'catalog-subcategory':
                {
                        $id = explode('|',$id);
                        $value = $id[0];
                        $cur_table = $id[1];
                        $nexttable = $id[2];
                        
            $q = "SELECT id, name FROM catalog_$nexttable WHERE catalog_".$cur_table."_id = '$value'";
                       //echo 'FALSE<$>'.$q;
            break;
                }
                 case 'get-location-wise-dealer':
		{  
                    $role_id = $_SESSION[SESS.'data']['role_id'];
                    $csess =  $_SESSION[SESS.'data']['id'];
                    $mydealer = new dealer();
                     if($role_id == 1)
                     $q = "SELECT id,name FROM  dealer_location_rate_list dlrl INNER JOIN dealer d ON d.id = dlrl.dealer_id WHERE location_id = '$id'";
                 else {
                      $dealer_data = $mydealer->get_user_wise_dealer_data($csess, $role_id);
                        if (!empty($dealer_data)) {
                            $dealer_data_str = implode(',', $dealer_data);
                            $filterdealer = " WHERE id IN ($dealer_data_str)";
                          $q = "SELECT id, name FROM user_dealer_retailer INNER JOIN dealer_location_rate_list USING(dealer_id) INNER JOIN dealer ON dealer_location_rate_list.dealer_id = dealer.id  $filterdealer AND location_id='$id' GROUP BY id ASC";
                        }
                    
                 } //else end here
                   break;
                   
		}
                //get-scheme-dealer-list
                case 'get-scheme-dealer-list':
		{
			$q = "SELECT dealer.id, dealer.name FROM  dealer INNER JOIN dealer_location_rate_list ON dealer_location_rate_list.dealer_id =dealer.id INNER JOIN location_3 ON location_3.id = dealer_location_rate_list.location_id INNER JOIN location_2 ON location_3.location_2_id = location_2.id WHERE location_2_id = {$id} GROUP BY dealer.id ASC";
                       
			break;
		}
                case 'company-catalog':
		{
			$q = "SELECT id,name from catalog_product where company_id = $id";                       
			break;
		}
                case 'get_batch_no':
		{
                        $id = explode('#', $id);
			$q = "SELECT batch_no, batch_no FROM user_primary_sales_order_details INNER JOIN user_primary_sales_order USING(order_id) WHERE product_id = '$id[0]' AND dealer_id = ".$_SESSION[SESS.'data']['dealer_id']." AND expiry_date > CURDATE() ";
                      // echo 'TRUE<$>'.$q;
		break;
		}
                 case 'get-challan-product':
		{
			$q = "SELECT CONCAT_WS('#',usod.product_id,usod.order_id ) AS id,CONCAT_WS(' ',c2.name,cp.name,cp.unit) as name FROM user_sales_order_details usod INNER JOIN user_sales_order uso ON usod.order_id = uso.order_id INNER JOIN catalog_product cp ON usod.product_id=cp.id INNER JOIN catalog_2 c2 ON c2.id = cp.catalog_id WHERE uso.retailer_id = '$id' AND uso.company_id = '{$_SESSION[SESS.'data']['company_id']}' AND order_status = '0' GROUP BY usod.product_id ASC";
                        //echo 'TRUE<$>'.$q;
			break;
		}
                
                case 'get_rolename':
		{
			$q = "SELECT role_id, rolename FROM _role WHERE role_group_id = '$id'";
			break;
		}
                case 'get_outlet':
		{
			$q = "SELECT id, outlet_type FROM _retailer_outlet_type ";
			break;
		}
                case 'get_parent_id':
		{
                        function get_parent_role($role_id)
                        {
                            global $dbc;
                            $qq = "SELECT role_id,rolename, senior_role_id FROM _role WHERE role_id='$role_id'";
                            $rr = mysqli_query($dbc ,$qq);
                            $rs = mysqli_fetch_assoc($rr);
                            $str = '';
                            if($rs['senior_role_id'] == 0) 
                            {
                               // $str .= $rs['role_id'];
                                return $str;
                            }
                            else{  
                                $str .= $rs['senior_role_id'].','.get_parent_role($rs['senior_role_id']);
                                return $str;
                            }

                        }
                       
                        $parent_id = get_parent_role($id);
                        $parent_id = rtrim($parent_id,',');
			$q = "SELECT role_id, rolename FROM _role WHERE role_id IN ($parent_id)";
                        //echo 'TRUE<$>'.$q;
			break;
		}
                case 'catalog-subcategory':
		{
                        $id = explode('|',$id);
                        $value = $id[0];
                        $cur_table = $id[1];
                        $nexttable = $id[2];
                        
			$q = "SELECT id, name FROM catalog_$nexttable WHERE catalog_".$cur_table."_id = '$value' AND company_id = '{$_SESSION[SESS.'data']['company_id']}' ";
                       //echo 'TRUE<$>'.$q;
			break;
		}
                case 'location-subcategory':
		{
                        $id = explode('|',$id);
                        $value = $id[0];
                        $cur_table = $id[1];
                        $nexttable = $id[2];
                       // $company = $_SESSION[SESS.'data']['company_id'];
			$q = "SELECT id, name FROM location_$nexttable WHERE location_".$cur_table."_id = '$value' ";
                       // echo 'FALSE<$>'.$q;
			break;
		}



        case 'location-subcategory-dealer-wise':
        {
            //$value = $id[0];
           // $cur_table = $id[1];
           // $nexttable = $id[2];
            $dealer_id = $_SESSION[SESS.'data']['dealer_id'];

            // $q = "SELECT id, name FROM location_$nexttable WHERE location_".$cur_table."_id = '$value' ";
            $q = "SELECT lv.l4_id, lv.l4_name FROM location_view lv 
            INNER JOIN dealer_location_rate_list dl ON lv.l5_id = dl.location_id 
            WHERE lv.l3_id =$id AND dl.dealer_id =$dealer_id GROUP BY lv.l4_id";
            // echo 'FALSE<$>'.$q;
            break;
        }


        case 'location-locality-dealer-wise':
        {
            $dealer_id = $_SESSION[SESS.'data']['dealer_id'];

            // $q = "SELECT id, name FROM location_$nexttable WHERE location_".$cur_table."_id = '$value' ";
            $q = "SELECT lv.l5_id, lv.l5_name FROM location_view lv 
            INNER JOIN dealer_location_rate_list dl ON lv.l5_id = dl.location_id 
            WHERE lv.l4_id =$id AND dl.dealer_id =$dealer_id GROUP BY lv.l5_id";
            // echo 'FALSE<$>'.$q;
            break;
        }


                case 'location-subcategory-expense':
		{
                        $id = explode('|',$id);
                        $value = $id[0];
                        $cur_table = $id[1];
                        $nexttable = $id[2];
			$q = "SELECT id, name FROM location_$nexttable WHERE location_".$cur_table."_id = '$value'";
                        //echo 'TRUE<$>'.$q;
                        $setvalidator = true;
			break;
		}
		//catalog-subcategory
                
                case 'get-location':
		{
                    //echo $id;
                        $id = explode('|',$id);
                        $value = $id[0];
                        $cur_table = $id[1];
                        
                        $q = "SELECT location_".$_SESSION[SESS.'retailer_level'].".id,location_".$_SESSION[SESS.'retailer_level'].".name FROM dealer_location_rate_list "
                                . "INNER JOIN location_".$_SESSION[SESS.'dealer_level']." ON location_".$_SESSION[SESS.'dealer_level'].".id=dealer_location_rate_list.location_id";
                        for($i = $_SESSION[SESS.'dealer_level'];$i < $_SESSION[SESS.'retailer_level'] ;$i++)
                        {
                            $j = $i + 1; 
                            $q .= " INNER JOIN location_$j ON location_$j.location_".$i."_id = location_$i.id ";
                        }
                        $q .= " WHERE dealer_location_rate_list.dealer_id=".$value;
                        //echo 'TRUE<$>'.$q;
			break;
		}   
                case 'get-retailer':
		{
                        $myobj = new retailer();
                        $sesId = $_SESSION[SESS.'data']['id'];
                        $role_id = $_SESSION[SESS.'data']['urole'];
                        $retailer_data = $myobj->get_user_wise_retailer_data($sesId , $role_id);
                        if(!empty($retailer_data)){
                            $retailer_data_str = implode(',' ,$retailer_data);
                            $q = "SELECT id,name FROM retailer WHERE retailer.location_id = '$id' AND id IN ($retailer_data_str)";
                            //echo 'TRUE<$>'.$q;
                        }
			break;
		}                
                case 'get-dealer-retailer':
		{
                $q = "SELECT id,name FROM retailer INNER JOIN user_dealer_retailer udr ON udr.retailer_id = retailer.id WHERE udr.dealer_id =".$_SESSION[SESS.'data']['dealer_id']."  AND location_id = '$id '";
                    break;
		}                
                case 'person-mtp':
		{
                    $dealer_location=$_SESSION[SESS.'dealer_level'];
                    $sesId = $_SESSION[SESS.'data']['id'];
                    $role_id = $_SESSION[SESS.'data']['urole'];
                    $myobj = new mtp();
                    $data = $myobj->get_user_wise_mtp_data($sesId , $role_id);
                    $filterstr = '';
                    if(!empty($data))
                    {
                        $data_str = implode(',' ,$data);
                        $filterstr = " AND person_id IN ($data_str)";
                    }
                    $q = "SELECT person.id, CONCAT_WS(' ',first_name,middle_name,last_name) AS name FROM location_$dealer_location INNER JOIN dealer_location_rate_list ON location_$dealer_location.id=dealer_location_rate_list.location_id  INNER JOIN user_dealer_retailer USING(dealer_id) INNER JOIN person ON person.id = user_dealer_retailer.user_id INNER JOIN monthly_tour_program ON monthly_tour_program.person_id = user_dealer_retailer.user_id  WHERE location_id = '$id' $filterstr GROUP BY person.id DESC";
                    
                    break;
		}
                
                case 'senior-by-location':
		{
                    $dealer_location=$_SESSION[SESS.'dealer_level'];
                    $retailer_level=$_SESSION[SESS.'dealer_level'];
                    $sesId = $_SESSION[SESS.'data']['id'];
                    $role_id = $_SESSION[SESS.'data']['urole'];
                    $myobj = new mtp();
                    $data = $myobj->get_user_wise_mtp_data($sesId , $role_id);
                    $filterstr = '';
                    if(!empty($data))
                    {
                        $data_str = implode(',' ,$data);
                        $filterstr = " AND person_id IN ($data_str)";
                    }
                    $q = "SELECT person.id, CONCAT_WS(' ',first_name,middle_name,last_name) AS name FROM location_$dealer_location INNER JOIN dealer_location_rate_list ON location_$dealer_location.id=dealer_location_rate_list.location_id  INNER JOIN user_dealer_retailer USING(dealer_id) INNER JOIN person ON person.id = user_dealer_retailer.user_id INNER JOIN monthly_tour_program ON monthly_tour_program.person_id = user_dealer_retailer.user_id  WHERE location_id = '$id' $filterstr GROUP BY person.id DESC";
                    
                    break;
		}
                case 'senior-by-location-circular':
		{
                    $dealer_location=$_SESSION[SESS.'dealer_level'];
                    $retailer_level=$_SESSION[SESS.'dealer_level'];
                    $sesId = $_SESSION[SESS.'data']['id'];
                    $role_id = $_SESSION[SESS.'data']['urole'];
                    $q = "SELECT person.id, CONCAT_WS(' ',first_name,middle_name,last_name) AS name FROM location_$dealer_location INNER JOIN dealer_location_rate_list ON location_$dealer_location.id=dealer_location_rate_list.location_id  INNER JOIN user_dealer_retailer USING(dealer_id) INNER JOIN person ON person.id = user_dealer_retailer.user_id INNER JOIN _role USING(role_id) WHERE location_id = '$id' AND role_group_id = 11 GROUP BY person.id DESC";
                    $setvalidator = TRUE;
                    break;
		}
                case 'user-details':
		{
                    $dealer_location=$_SESSION[SESS.'dealer_level'];
                    $sesId = $_SESSION[SESS.'data']['id'];
                    $role_id = $_SESSION[SESS.'data']['urole'];
                    $myobj = new sale();
                    $data = $myobj->get_user_wise_expense_data($sesId , $role_id);
                    $filterstr = '';
                    if(!empty($data))
                    {
                        $data_str = implode(',' ,$data);
                        $filterstr = " AND person_id IN ($data_str)";
                    }
                    $q = "SELECT person.id, CONCAT_WS(' ',first_name,middle_name,last_name) AS name FROM location_$dealer_location INNER JOIN dealer_location_rate_list ON location_$dealer_location.id=dealer_location_rate_list.location_id  INNER JOIN user_dealer_retailer USING(dealer_id) INNER JOIN person ON person.id = user_dealer_retailer.user_id INNER JOIN user_expense_report ON user_expense_report.person_id = person.id  WHERE location_id = '$id' $filterstr GROUP BY person.id DESC";
                    //echo 'TRUE<$>'.$q;
                    $setvalidator = true;
                    break;
		}  
                case 'user-tracking-details':
		{
                    $dealer_location=$_SESSION[SESS.'dealer_level'];
                    $sesId = $_SESSION[SESS.'data']['id'];
                    $role_id = $_SESSION[SESS.'data']['urole'];
                    $myobj = new sale();
                    $data = $myobj->get_user_wise_tracking_data($sesId , $role_id);
                    $filterstr = '';
                    if(!empty($data))
                    {
                        $data_str = implode(',' ,$data);
                        $filterstr = " AND user_daily_tracking.user_id IN ($data_str)";
                    }
                    $q = "SELECT person.id, CONCAT_WS(' ',first_name,middle_name,last_name) AS name FROM location_$dealer_location INNER JOIN dealer_location_rate_list ON location_$dealer_location.id=dealer_location_rate_list.location_id  INNER JOIN user_dealer_retailer USING(dealer_id) INNER JOIN person ON person.id = user_dealer_retailer.user_id INNER JOIN user_daily_tracking ON user_daily_tracking.user_id = person.id  WHERE location_id = '$id' $filterstr GROUP BY person.id DESC";
                    //echo 'TRUE<$>'.$q;
                    $setvalidator = true;
                    break;
		}
                case 'location-subcategory-attendence':
		{
                        $id = explode('|',$id);
                        $value = $id[0];
                        $cur_table = $id[1];
                        $nexttable = $id[2];
			$q = "SELECT id, name FROM location_$nexttable WHERE location_".$cur_table."_id = '$value' AND company_id = '{$_SESSION[SESS.'data']['company_id']}'";
                        $setvalidator = true;
			break;
		}
               
                case 'person-by-senior':
		{
                        $dealer_location = $_SESSION[SESS.'dealer_level'];
                        $sesId = $_SESSION[SESS.'data']['id'];
                        $role_id = $_SESSION[SESS.'data']['urole'];
                        $myobj = new settings();
                        $data = $myobj->get_user_wise_attendence_data($sesId , $role_id);
                        $filterstr = '';
                        if(!empty($data))
                        {
                            $data_str = implode(',',$data);
                            $filterstr = " AND user_id IN ($data_str)";
                        }
                        $q = "SELECT person.id, CONCAT_WS(' ',first_name,middle_name,last_name) AS name FROM location_$dealer_location INNER JOIN dealer_location_rate_list ON location_$dealer_location.id=dealer_location_rate_list.location_id  INNER JOIN user_dealer_retailer USING(dealer_id) INNER JOIN person ON person.id = user_dealer_retailer.user_id WHERE location_id = '$id' $filterstr GROUP BY person.id DESC";
                        //echo 'TRUE<$>'.$q;
                        $setvalidator = true;
			break;
		} 
                case 'get-dealer-wise-location':
		{
                     $dealer_level = $_SESSION[SESS.'dealer_level'];
                     $retailer_level = $_SESSION[SESS.'retailer_level'];
                     
                     $sesId = $_SESSION[SESS.'data']['id'];
                     $role_id = $_SESSION[SESS.'data']['urole'];
                     $q = "SELECT location_$retailer_level.id,location_$retailer_level.name FROM dealer_location_rate_list INNER JOIN location_$dealer_level ON location_$dealer_level.id = dealer_location_rate_list.location_id";
                     for($i = $dealer_level; $i<$retailer_level;$i++) {
                        $k = $i + 1;
                        $q .= " INNER JOIN location_$k ON location_$k.location_".$i."_id = location_$i.id ";
                     }
                     $q .= " WHERE dealer_id = '$id'";
                    
                     break;
		}
             case 'get_saleable_non_saleable':
        {
                 
                      $q = "SELECT `id`,`name` FROM `complaint_type` WHERE  `saleable_non_saleable`=$id";
                    //echo 'TRUE<$>'.$q;
                     break;
        }
                
	}
    
	$r = mysqli_query($dbc, $q);
	if($r){
		if(mysqli_num_rows($r)>0){
			echo'TRUE<$>';
			if($setvalidator) 
                            $str = '<$$$>== ALL ==<$$>';
                        else
                        {    
                           // if($wcase !='get_product_mrp')
                            $str = '<$$$>== Please Select ==<$$>';
                        }
                            while($row = mysqli_fetch_row($r)){
				$str .= $row[0].'<$$$>'.$row[1].'<$$>';
			}
			$str = rtrim($str, '<$$>');
            //print_r($row);
			echo $str;
		}// if(mysqli_num_rows($r)>0){ ends
		else
			echo'FALSE<$>Sorry no '.$wcase.' found for selected option';
	}// if($r){ ends
}
else
	echo'FALSE<$>Please select a value';
$output = ob_get_clean();
echo $output = trim($output);	
?>