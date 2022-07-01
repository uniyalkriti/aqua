<?php	
// Test URL
//http://localhost/msell/webservices/signin.php?imei=123456&uname=anil123&pass=anil123
// echo "1";die;
require_once('../admin/functions/common_function.php');
require_once('../admin/include/conectdb.php');
require_once('../admin/include/config.inc.php');
require_once('../admin/include/my-functions.php');
// function __autoload($class)
// {	
// 	require_once('../admin/include/classes/'.strtolower($class) .'.php');
// }

// $myobj = new mtp();
$imei = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['imei'])));
$uname = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['uname'])));
$pass = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['pass'])));
//$rest = substr($imei, -5) * 1; // multiplying by 1 for termination starting zero values
// echo $dbc; die;
    
$query_user_login = "SELECT dpl.dealer_id,dpl.email,dpl.phone,dpl.state_id,dpl.dpId as id,_role.rolename as rolename,dpl.role_id ,dpl.person_name,dpl.company_id FROM `dealer_person_login` as dpl 
INNER JOIN _role ON _role.role_id=dpl.role_id
 AND uname = '$uname' AND AES_DECRYPT(pass, 'demo') = '$pass'  AND  activestatus= '1'  ORDER BY person_name ASC";
 // h1($query_user_login);//dealer_person_login
$user_qry = mysqli_query($dbc, $query_user_login);

if($user_qry && mysqli_num_rows($user_qry) > 0)
{
    
    $udata=  mysqli_fetch_array($user_qry);
    $user_id = $udata['id'];
    $person_fullname = $udata['person_name'];
    $mobile = $udata['phone'];
    $email = $udata['email'];
   // $senior_id = $udata['person_id_senior'];
    $state_id = $udata['state_id'];
    $person_role_id = $udata['role_id'];
    $person_role_name = $udata['rolename'];
    $dealer_id = $udata['dealer_id'];
    $company_id = $udata['company_id'];
    $q_first_login = "UPDATE person SET imei_number = '$imei' WHERE id = '$user_id'";
    $r_first_login = mysqli_query($dbc , $q_first_login);
 
    



$essential = array();
$final_working_deatails = array();
$working = array();
$user_info = array();

       
       function get_parent_child($parent_id)
         {
             global $dbc;
            $q = "SELECT name FROM _working_status WHERE id = $parent_id";
             $r = mysqli_query($dbc,$q);
             if($r){
                 $row1 = mysqli_fetch_assoc($r);
                 return $row1['name'];
             }
         }

 $compa = array();
    $final_compa_details = array();

    $cdataa = "SELECT id, name FROM user_category";
    $cquerya = mysqli_query($dbc, $cdataa);
    if ($cquerya) {
        while ($complaina = mysqli_fetch_assoc($cquerya)) {
            $compa['id'] = $complaina['id'];
            $compa['name'] = $complaina['name'];
            $final_compa_details[] = $compa;
        }
    }
      
 $comp = array();
    $final_comp_details = array();

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
      

        
    $catalog_array = array();
    $final_catalog_details = array();
    $q = "SELECT id, name FROM catalog_1 ORDER BY name ASC";
    $r = mysqli_query($dbc, $q);
    if ($r) {
        while ($catlog = mysqli_fetch_assoc($r)) {
            $catalog_array['id'] = $catlog['id'];
            $catalog_array['name'] = $catlog['name'];
            $final_catalog_details[] = $catalog_array;
        }
    }

$catalog_product_info=array();
$final_catalog_product_details=array();
  $query_catalog_product ="SELECT catalog_2.id as cid ,catalog_product.id as id, catalog_product.name, catalog_product.base_price AS mrp, base_price_per,unit,packing_type,base_price_per,gst_percent,quantity_per_case,taxable,dealer_rate,dealer_pcs_rate,retailer_rate,mrp_pcs,retailer_pcs_rate,
catalog_product.company_id,catalog_product.division    FROM catalog_product 
INNER JOIN  catalog_2 on catalog_product.catalog_id=catalog_2.id 
INNER JOIN product_rate_list ON product_rate_list.product_id=catalog_product.id
where catalog_product.company_id = '$company_id'
 GROUP BY product_rate_list.product_id ";
 // h1($query_catalog_product);
//    echo $query_catalog_product = "SELECT catalog_1.id as cid,cs.piece,cs.cases,catalog_product.id as id, catalog_product.name, catalog_product.base_price AS mrp,unit,division, IF(focus.product_id IS NULL,0,1) AS focus_status,start_date,end_date,
// (select rl.rate from product_rate_list as rl where rl.product_id=product_rate_list.product_id and rl.price_type='1' and rl.state_id=product_rate_list.state_id LIMIT 1) as base_price,
// (select prl.product_mrp from product_rate_list as prl where prl.product_id=product_rate_list.product_id and prl.price_type='1' and prl.state_id=rate_list.state_id LIMIT 1) as product_mrp,
// (select r_l.rate from product_rate_list as r_l where r_l.product_id=product_rate_list.product_id and r_l.price_type='2' and r_l.state_id=product_rate_list.state_id LIMIT 1) as piece_base_price,
// (select pr_l.product_mrp from product_rate_list as pr_l where pr_l.product_id=product_rate_list.product_id and pr_l.price_type='2' and pr_l.state_id=product_rate_list.state_id LIMIT 1) as piece_product_mrp
// FROM catalog_product inner join catalog_1 on catalog_product.catalog_id=catalog_1.id 
// LEFT JOIN focus ON focus.product_id=catalog_product.id 
// INNER JOIN product_rate_list ON product_rate_list.product_id=catalog_product.id 
// INNER JOIN cases as cs ON cs.product_id=catalog_product.id 
// WHERE  product_status=1 GROUP BY rate_list.product_id 
// ORDER BY seq_id ASC";
//ORDER BY division,catalog_product.id ASC";
   // h1($query_catalog_product);
    $pack_size_map = get_my_reference_array('pack_size', 'id', 'name');
    $run_catalog_product = mysqli_query($dbc, $query_catalog_product);
    if (mysqli_num_rows($run_catalog_product) > 0) {
        while ($catalog_product_fetch = mysqli_fetch_assoc($run_catalog_product)) {
            $catalog_product_info['id'] = $catalog_product_fetch['id'];
            $catalog_product_info['name'] = $catalog_product_fetch['name'];
            $catalog_product_info['base_price'] = $catalog_product_fetch['retailer_rate'];
            $catalog_product_info['mrp'] = $catalog_product_fetch['mrp'];

            $catalog_product_info['piece_base_price'] = $catalog_product_fetch['retailer_rate'];
            $catalog_product_info['piece_product_mrp'] = $catalog_product_fetch['mrp_pcs'];

            $catalog_product_info['unit'] = $catalog_product_fetch['unit'];
            $catalog_product_info['category'] = $catalog_product_fetch['cid'];
           $catalog_product_info['taxable'] = $catalog_product_fetch['taxable'];
             $catalog_product_info['dealer_rate'] = $catalog_product_fetch['dealer_rate'];
            $catalog_product_info['dealer_pcs_rate'] = $catalog_product_fetch['dealer_pcs_rate'];
       $catalog_product_info['retailer_rate'] = $catalog_product_fetch['retailer_rate'];
       $catalog_product_info['retailer_pcs_rate'] = $catalog_product_fetch['retailer_pcs_rate']; //retailer_pcs_rate
           // $catalog_product_info['cases'] = $catalog_product_fetch['cases'];
            $catalog_product_info['case_quantity'] = $catalog_product_fetch['quantity_per_case'];
            $catalog_product_info['product_division'] = "0";
         
            $final_catalog_product_details[] = $catalog_product_info;
        }
    }
    $query_category = "SELECT id,name,company_id  FROM catalog_2 where company_id = '$company_id'  ORDER BY id ASC";
    // h1($query_category);
    $run_category = mysqli_query($dbc, $query_category) or die(mysqli_error($dbc));
    if (mysqli_num_rows($run_category) > 0) {
        while ($category_fetch = mysqli_fetch_assoc($run_category)) {
            $category_info['id'] = $category_fetch['id'];
            $category_info['name'] = $category_fetch['name'];
            $category_info['classification'] = $category_fetch['company_id'];
            $final_category_details[] = $category_info;
        }
    }

       
                $essential[] = array("response"=>"TRUE"
                            ,"user_id"=>$user_id 
                            ,"company_id"=>$company_id 
                             ,"user_name"=>$person_fullname                  
                            ,"user_role_id"=>$person_role_id
                            ,"user_role_name"=>$person_role_name             
                            ,"dealer_id"=>$dealer_id   
                            ,"mobile"=>!empty($mobile)?$mobile:''   
                            ,"email"=>$email 
                           , "product_category"=>!empty($final_category_details)?$final_category_details:array()
                           , "product" => $final_catalog_product_details
                           , "user_category" => $final_compa_details
                            , "complaint" => $final_comp_details    
                );   
                 
	
	} 
        
        else
        {
            $essential[] = array("response"=>"FALSE"); 
        }
	$final_array = array("result"=>$essential);	

	$data = json_encode($final_array);
        echo $data;
	//pre($final_array);
?>
