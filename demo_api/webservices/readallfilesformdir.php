<?php
require_once('../admin/include/conectdb.php');
require_once('functions.php');
$results_array= array();
//$filename=$_GET['filename'];
$directory = "mobile_sync";

if (is_dir($directory))
{
    foreach(glob($directory.'/*.*') as $file) {
        $results_array[] = $file;
    }
}
 foreach($results_array as $value)
{
    $file = fopen($value, "r") or die("Unable to open file!");
    while(!feof($file)){
        $line = fgets($file);
        # do same stuff with the $line
        $urls=explode('##|##',$line);
        foreach ($urls as $key => $val){
            if(strpos($val, 'err') !== false)
            {
                $errors=explode('#=#',$val);
               // echo $errors[0]."--".$errors[1]."--".$errors[2]."<br>";
    //            mysql_query("INSERT INTO `mobile_errors` set `imei`='".$errors[1]."',`error_date`='".$errors[2].", `error_time`='".$errors[3].", `error_details`='".$errors[4]."'") or die("error");
                  $error= htmlspecialchars($errors[4], ENT_QUOTES);
                 // echo "INSERT INTO `mobile_errors` set `imei`='".$errors[1]."', `error_details`='".$error."'";
                  mysqli_query($dbc,"INSERT INTO `mobile_errors` set `imei`='".$errors[1]."',`err_date`='".$error[2]."',`err_time`='".$error[3]."' `error_details`='".$error."'") or die(mysqli_error());            
            }else{
                //mysql_query($val);
                //echo $val."<br>";
                if($val!=""){
                   // echo SERVER_PATH."/webservices/".$val;
                    echo $res = file_get_contents("http://localhost/msell/webservices/".$val);
                }
            }
        }
    }
    fclose($file);
}
?>