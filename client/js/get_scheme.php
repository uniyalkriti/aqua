<?php 
@session_start();
ob_start();

if(isset($_GET['product']) && !empty($_GET['product']))
{
	$id = $_GET['product'];
	$quantity = $_GET['quantity'];
	//connect to the database
        require_once('../include/config.inc.php');
         require_once('../include/conectdb.php');
       // require_once(BASE_URI_ROOT.ADMINFOLDER.MSYM.'include'.MSYM.'my-functions.php');
        //include('../../include/my-functions.php');	
        //require_once(BASE_URI.'include'.MSYM.'my-functions.php');
        $setvalidator = false;
	global $dbc;
       $q = "SELECT scheme_quantity from scheme_product_details INNER JOIN scheme_dealer_details ON scheme_product_details.scheme_id = scheme_dealer_details.sd_id WHERE product_id = $id and buy_quantity= $quantity AND start_date<= CURDATE() AND end_date>= CURDATE()  "; 
      
        $res = mysqli_query($dbc, $q);
        if(mysqli_num_rows($res)>0){
            $row = mysqli_fetch_array($res);
            $str = $row['scheme_quantity'];
            echo $str;
        }else{
            $str = 0;
            echo $str;
        }
}

?>