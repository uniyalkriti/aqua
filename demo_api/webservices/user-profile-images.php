<?php
//TEST URL - http://localhost/suryasteel/webservices/user-profile-images.php?imei=1342537475886996&image_name=mukesh.jpg&image_file=xyz

require_once('../admin/include/conectdb.php');
require_once('functions.php');
if(isset($_GET['imei'])) $imei = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['imei']))); else $imei = 0;
$user_qry=mysqli_query($dbc,"select id from person where imei_number='".$imei."'");
$user_res=  mysqli_fetch_assoc($user_qry);
$user_id=$user_res['id'];
if(isset($user_id) && !empty($user_id))
{
        if(isset($_GET['image_name'])) $image_name = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['image_name']))); else $image_name = '';
        if(isset($_REQUEST['image_file'])) $image_file = mysqli_real_escape_string($dbc, trim(stripslashes($_REQUEST['image_file']))); else $image_file = '';
    
    $binary=base64_decode($image_file);
    header('Content-Type: bitmap; charset=utf-8');
    $file = fopen('./mobile_images/'.$image_name, 'wb');

    if(fwrite($file, $binary))
        {
            fclose($file);
            echo "Image Uploaded Succesfully";
        }
        else 
        {
            fclose($file);
            echo "Image not uploaded succesfully";
        }
}
else 
{
    echo "Image not uploaded succesfully";
}

?>