<?php

require_once('../../admin/include/conectdb.php');
function get_total_user($id,$role_id){
global $dbc;
//print_r($_SESSION);

$user_id=$id;

 function recursiveall2($code){
        global $dbc;
        //static $data;
        $qry="";
        $res1="";
        $res2="";
       $t="select id  from person where  person_id_senior=trim('".$code."') order by id asc "; 
    
        $qry=mysqli_query($dbc,$t);
        $num=mysqli_num_rows($qry);
        if($num<=0){
            $res1=mysqli_fetch_assoc($qry);
            if($res1['id']!=""){
                $_SESSION['juniordata'][]= "'".$res1['id']."'";
            }
        }
        else
        {
            while($res2=mysqli_fetch_assoc($qry)){
                if($res2['id']!=""){
                    $_SESSION['juniordata'][]= "'".$res2['id']."'";
                   recursiveall2($res2['id']);
                }
            }
        }
        return array_unique($_SESSION['juniordata']);	
    }
 unset($_SESSION['juniordata']);
         $uservalue = recursiveall2($id);
        
       
      if(!empty($uservalue)){

            $newdata = implode(',',$uservalue);
            
           $userid =$newdata.','.$id;
         
            $datanew = "($userid)";

            }
            else{
                $datanew = "($id)";
            }


    $location_3=$_SESSION['patanjalidata']['location_3_id'];
    $location_1 = '';
    if(isset($_POST['date_range'])){
        $date_array=  explode('-', $_POST['date_range']);
        $from_range = date("Y-m-d", strtotime($date_array[0]));
        $to_range = date("Y-m-d", strtotime($date_array[1]));
        
       // $from_range = '2018-10-01';
      //  $to_range = '2018-10-12';
        $date_range="DATE_FORMAT(work_date,'%Y-%m-%d')>='$from_range' AND DATE_FORMAT(work_date,'%Y-%m-%d')<='$to_range'";
        $date_range1="DATE_FORMAT(date,'%Y-%m-%d')>='$from_range' AND DATE_FORMAT(date,'%Y-%m-%d')<='$to_range'";
    }else{
      //  $from_range=date('Y-m-d');
      //  $to_range=date('Y-m-d');
        
         $from_range = date('Y-m-d');
        $to_range = date('Y-m-d');
        $date_range="DATE_FORMAT(work_date,'%Y-%m-%d')>='$from_range' AND DATE_FORMAT(work_date,'%Y-%m-%d')<='$to_range'";
        $date_range1="DATE_FORMAT(date,'%Y-%m-%d')>='$from_range' AND DATE_FORMAT(date,'%Y-%m-%d')<='$to_range'";
    }	

//**********************************************************************************************//
    //*********************************************TRANSACTION*************************************
    $company_id_fetch = "SELECT company_id from person where id = $id ";
    $role_company = mysqli_query($dbc,$company_id_fetch);
    $company_id_object = mysqli_fetch_object($role_company);

    $company_id = $company_id_object->company_id;

    // print_r($company_id); die;
    $division_wise_sale=array();
		
         $qry = "SELECT COUNT(DISTINCT p.id) as total_users,
        (select COUNT(DISTINCT uda.user_id) as total_users_working FROM user_daily_attendance uda INNER JOIN person pr on uda.user_id=pr.id INNER JOIN person_login pl on pr.id=pl.person_id  WHERE pl.person_status=1 AND $date_range AND uda.user_id IN $datanew AND uda.company_id = $company_id) as total_users_working,
         (select count(DISTINCT person.id) from person INNER JOIN person_login ON person.id=person_login.person_id where  person_status=1 AND  person.id IN $datanew AND person.company_id = $company_id) as total_sales_team,
         (select count(DISTINCT uda1.user_id) from person INNER JOIN user_daily_attendance uda1 ON person.id=uda1.user_id INNER JOIN dealer_location_rate_list dlrl ON dlrl.user_id=uda1.user_id
		 INNER JOIN location_view lv ON lv.l7_id=dlrl.location_id where role_id in(12,17,34,39,40,42,45,46) AND $date_range AND  uda1.user_id IN $datanew) as total_sales_team_working,
         (select count(DISTINCT daily_reporting.user_id) from daily_reporting where $date_range AND  daily_reporting.user_id IN $datanew) as total_user_reporting,
        (select count(DISTINCT user_sales_order.user_id) from user_sales_order 
		 INNER JOIN location_view lv ON lv.l7_id=user_sales_order.location_id where $date_range1 AND  user_sales_order.user_id IN $datanew) as totaluser_sales_count,
         (SELECT ROUND(SUM(rate*quantity),2) as total_sale FROM `user_sales_order` uso 
            INNER JOIN user_sales_order_details USING(order_id) INNER JOIN location_view lv ON lv.l7_id=uso.location_id 
            WHERE $date_range1 AND uso.user_id IN $datanew) as total_sale_value         
		 FROM `person` as p INNER JOIN person_login as pl ON pl.person_id=p.id INNER JOIN dealer_location_rate_list dlrl ON dlrl.user_id=p.id
		 INNER JOIN location_view lv ON lv.l7_id=dlrl.location_id WHERE pl.person_status=1 AND role_id>1  ";
          // exit;
		$result = mysqli_query($dbc,$qry);
	
		$row=mysqli_fetch_object($result);
     
        
        //**********************************State WISE DATA**************************************************//
       //******************************************************************************************//
        $sale_stae_array=array();
   
        $l1query="SELECT l1_name as lname ,l1_id as lid FROM `location_view` AS lv 
        INNER JOIN dealer_location_rate_list AS dlrl ON  dlrl.location_id=lv.l7_id  
        WHERE  dlrl.user_id IN $datanew  AND uso.company_id = $company_id group by lv.l1_id ";
   
        $l1_result = mysqli_query($dbc,$l1query);
        while($l1_row = mysqli_fetch_object($l1_result)){
        
            $salestq="SELECT ROUND(SUM(total_sale_value),2) as total_sale FROM `user_sales_order` uso 
            INNER JOIN user_sales_order_details USING(order_id) INNER JOIN location_view lv ON lv.l7_id=uso.location_id 
            WHERE $date_range1 AND uso.company_id = $company_id AND  uso.user_id IN $datanew AND lv.l1_id=$l1_row->lid";  
           
            $sale_result1 = mysqli_query($dbc,$salestq);
         
            $total_sale1 = mysqli_fetch_object($sale_result1);
            if(empty($location_1)) {
                $sale_stae_array[$l1_row->lname] = $total_sale1->total_sale;
            }else{
                $sale_stae_array[$l1_row->lid] = $total_sale1->total_sale;
            }
	}
            $row->state_wise_secondry_sale =$sale_stae_array;

		//**********************************CITY WISE DATA**************************************************//
       //******************************************************************************************//
        $sale_array=array();
        $l3_result = mysqli_query($dbc, "SELECT l3_name AS lname ,l3_id AS lid FROM `location_view` AS lv 
        INNER JOIN dealer_location_rate_list AS dlrl ON  dlrl.location_id=lv.l7_id  WHERE dlrl.user_id IN $datanew group by l3_id");
 
	while($l3_row = mysqli_fetch_object($l3_result)){
          
            $salequery="SELECT ROUND(SUM(total_sale_value),2) as total_sale FROM `user_sales_order` uso 
            INNER JOIN user_sales_order_details USING(order_id) INNER JOIN location_view lv ON lv.l7_id=uso.location_id 
            WHERE $date_range1 AND uso.company_id = $company_id  AND uso.user_id IN $datanew AND lv.l3_id=$l3_row->lid";
                $sale_result = mysqli_query($dbc,$salequery );
                $total_sale = mysqli_fetch_object($sale_result);       
                $sale_array[$l3_row->lname] = $total_sale->total_sale;           
            }
	$row->city_wise_secondry_sale =$sale_array;

    //**********************************PRODUCT CATEGORY WISE DATA**************************************************//
    //******************************************************************************************//
    $catlog_array=array();
    $catlog_result = mysqli_query($dbc,"SELECT * FROM `catalog_0` where company_id = $company_id");
    while($catlog_row = mysqli_fetch_object($catlog_result)){      
        $qr="SELECT SUM(quantity) as case_quantity, ROUND(SUM(usod.rate*usod.quantity),2) as catlog_total_sale FROM `user_sales_order` uso 
        INNER JOIN user_sales_order_details usod USING(order_id) INNER JOIN catalog_view cv ON cv.product_id=usod.product_id 
        INNER JOIN location_view lv ON lv.l7_id=uso.location_id
        WHERE $date_range1 AND uso.user_id IN $datanew AND cv.c0_id=$catlog_row->id "; 
        
        /* $qr="SELECT SUM(quantity) as case_quantity, ROUND(SUM(uso.rate*uso.quantity),2) as catlog_total_sale FROM `sale_order_product_view` uso 
        INNER JOIN catalog_view cv ON cv.product_id=uso.product_id        
        WHERE $date_range1 AND uso.$datanew AND cv.c1_id=$catlog_row->id "; */
         
              // echo $qr; die;
        $catlog_sale_result = mysqli_query($dbc,$qr );         
     
        $cat_total_sale = mysqli_fetch_object($catlog_sale_result);
        $prod_id= [$catlog_row->id];
        $catlog_array[$catlog_row->name]['product_id']=$catlog_row->id; 
        $catlog_array[$catlog_row->name]['total_sale']=$cat_total_sale->catlog_total_sale;  
        $catlog_array[$catlog_row->name]['case_quantity']=$cat_total_sale->case_quantity; 
        
    }
    $row->classification_sale =$catlog_array;
    
    //*********************Attendance DATA**************************************************//
    //******************************************************************************************//
    $attendane_result=array();
    $month=date('m-Y');
    if($from_range==$to_range){$attn_range="DATE_FORMAT(`work_date`,'%m-%Y')='$month'";}
    else{$attn_range=$date_range;}
  
    $attq="SELECT DATE_FORMAT(`work_date`,'%d-%m-%Y') as wdate,COUNT(DISTINCT user_id) as total_working 
    FROM user_daily_attendance WHERE $attn_range AND user_daily_attendance.company_id = $company_id AND user_id IN $datanew GROUP BY wdate";
     //echo $attq; die;
     $attendane_qry = mysqli_query($dbc,$attq );  
    while($attendance_row = mysqli_fetch_object($attendane_qry)){
        $attendane_result[$attendance_row->wdate]=$attendance_row->total_working;
    }   
    $row->attendance=$attendane_result;    
    return $row;


}

  






?>
