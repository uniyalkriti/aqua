<?php
require_once('../admin/functions/common_function.php');
require_once('../admin/include/conectdb.php');
require_once('../admin/include/config.inc.php');
require_once('../admin/include/my-functions.php');

 $product = array();
  $q = "SELECT id,name,image_name FROM catalog_product";
// h1($q); exit;
  $res1=  mysqli_query($dbc, $q);  
$count = mysqli_num_rows($res1);
//echo $count;
       while($row = mysqli_fetch_assoc($res1))
        {  
            $ret_info['product_id'] = $row['id'];
           $ret_info['product_name'] = $row['name'];
           $ret_info['image_name'] = $row['image_name'];
	   $path = '../image_product/'.$row['image_name'].'.png';
//echo  $path;
	   $type = pathinfo($path, PATHINFO_EXTENSION);
	   $data = file_get_contents($path);
           $ret_info['image'] = 'data:image/' . $type . ';base64,' . base64_encode($data);
	
//echo $ret_info['image']; exit;
	//$type = pathinfo("../image_product/".$ret_info['image_name'].".png", PATHINFO_EXTENSION);
	//$ret_info['image'] = 'data:image/' . $type . ';base64,' .base64_encode(file_get_contents("../image_product/".$ret_info['image_name'].".png"));
           $product[] = $ret_info;
	//$product[]= str_replace("\/","/",$product1);
         
        }
        
         
        
     $final_array = array("result"=>$product);	
     $data = json_encode($final_array);
    // print_r($dispatch_payment_details);
     echo stripslashes($data);
    

?>

