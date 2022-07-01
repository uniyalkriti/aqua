<?php

require_once('../../admin/include/conectdb.php');
global $dbc;


 function recursiveall2($code){
        global $dbc;
        //static $data;
        $qry="";
        $res1="";
        $res2="";
       $t="select id  from person where person_id_senior=trim('".$code."') order by id asc "; 
    
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
            
  function get_catalog1_details($user_id,$date,$catalog_id){      
    //  echo "manisha"; exit;  
      global $dbc;
        $out = array();     
        
        $date_range=  explode('-', $date);
        $from_range = date("Y-m-d", strtotime($date_range[0]));
        $to_range = date("Y-m-d", strtotime($date_range[1]));
        
        recursiveall2($user_id);   
        $senior = join(',', array_unique($_SESSION['juniordata']));
        if(empty($senior)) {$senior = 0; }
        unset($_SESSION['juniordata']);
          
                    
        $query="select usod.dealer_name,usod.retailer_name,usod.user_name as full_name,
                                cv.product_name as cp_name,sum(uso.quantity) as quantity,
                                sum(uso.rate*uso.quantity) as total_value 
                                from sale_order_product_view uso 
                                INNER JOIN sale_order_dealer_view usod ON usod.order_id=uso.order_id                                 
                                INNER JOIN catalog_view as cv ON cv.product_id=uso.product_id
                                where uso.date >='$from_range' and uso.date <= '$to_range' 
                                and cv.c0_id='$catalog_id' and uso.user_id in ($senior,$user_id) and uso.quantity != '0' 
                                group by cv.product_id,uso.retailer_id,uso.dealer_id,usod.dealer_name,usod.retailer_name,usod.user_name, cv.product_name 
                                order by usod.user_name,usod.dealer_name,usod.retailer_name asc"; 
                               // exit;
                                
        $rs = mysqli_query($dbc,$query);
 
        $id = 1;
        while($row = mysqli_fetch_object($rs))
        {         
         
            $out[$id] = $row; // storing the person id
            $id++;
        }
     
        return $out;
    }

  






?>
