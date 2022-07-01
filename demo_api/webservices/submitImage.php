<?php	

//TEST URL - http://localhost/webservices/retailer-multi-image.php?imei=1342537475886996&retailer_id=3&image_file=raj&image_name=mukesh.jpg&date_time=2014-06-01 08:44:21
require_once('../admin/include/conectdb.php');
require_once('functions.php');

if(isset($_GET['imei'])) $imei = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['imei']))); else $imei = 0;
$user_qry=mysqli_query($dbc,"select id from person where imei_number='".$imei."'");
$user_res=  mysqli_fetch_assoc($user_qry);
$user_id=$user_res['id'];

$num = mysqli_num_rows($user_qry);
if($num>0){
    if(isset($_GET['image_name'])) $image_name = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['image_name']))); else $image_name = '';
    if(isset($_REQUEST['image_file'])) $image_file = mysqli_real_escape_string($dbc, trim(stripslashes($_REQUEST['image_file']))); else $image_file = '';
    if(isset($_GET['image_source'])) $image_source = mysqli_real_escape_string($dbc, trim(stripslashes($_REQUEST['image_source']))); else $image_source= '';

    //$base=$_REQUEST['image'];
    //$imgname = $_GET['img'];
    $binary=base64_decode($image_file);
    header('Content-Type: bitmap; charset=utf-8');
    
    switch ($image_source){
        case 'Sales':
        {
        $path='../myuploads/retailer/dsr/';
        break;
        }
        case 'New-Retailer':
        {
        $path='../myuploads/retailer/create/';
        break;
        }        
    }

    $file = fopen($path.$image_name, 'wb');
    //fwrite($file, $binary);

    if(fwrite($file, $binary))
    {        
        echo "Image Upload Succesfully Completed";
    }
    else{
        echo "Image Not uploaded Some Error occurred";
    }
    fclose($file);
}
?>
