<?php
require_once('../admin/include/conectdb.php');
require_once('functions.php');
require_once('../admin/include/config.inc.php');

$results_array= array();
$filename=$_GET['filename'];
$directory = "mobile_sync";
$path=$directory."/".$filename.".txt";
    $file = fopen($path, "r") or die("Unable to open file!");
    while(!feof($file)){
        $line = fgets($file);
        # do same stuff with the $line
        $urls=explode('##|##',$line);
        foreach ($urls as $key => $val){
            if(strpos($val, 'moberr') !== false)
            {
                  $errors=explode('#=#',$val);
                  $error= htmlspecialchars($errors[4], ENT_QUOTES);
                  mysqli_query($dbc,"INSERT INTO `mobile_errors` set `imei`='".$errors[1]."',`err_date`='".$error[2]."',`err_time`='".$error[3]."', `error_details`='".$error."'") or die(mysqli_error());            
            }else{
                if($val!=""){
                    echo $res = file_get_contents("http://localhost/msell-dsgroup/webservices/".$val);
                    //echo $val;exit;
                   //echo $res = file_get_contents(BASE_URL.$val);  
                }
            }
        }
    }
fclose($file);
?>