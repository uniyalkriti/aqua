<?php
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once('../admin/functions/common_function.php');
require_once('../admin/include/conectdb.php');
require_once('../admin/include/config.inc.php');
require_once('../admin/include/my-functions.php');

$myobj = new mtp();
$user_id = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['user_id'])));

$q = "select p.id,pl.circular_id from person p, person_login pl where p.id = '$user_id' AND p.id = pl.person_id ";
//h1($q);
$res = mysqli_query($dbc, $q);
$row = mysqli_fetch_array($res);
$person_id = $row['id'];
$c_string = $row['circular_id'];
$result = array();
$final_result = array();
$circular_id = array();
$circular_str = '';

if(!$c_string=='')
{   
    $circular_id = explode(',', $c_string);
    foreach ($circular_id as $val) {
        $q = "SELECT id,content,title,image  FROM circular WHERE id = $val AND circular_type ='notifi' AND status ='Publish'";
       //h1($q);
        $r = mysqli_query($dbc, $q);
        if ($r && mysqli_num_rows($r) > 0) {
            $row1 = mysqli_fetch_assoc($r);
            $result['id'] = $row1['id'];
            $result['title'] = $row1['title'];
            $result['content'] = $row1['content'];
            if(empty($row1['image'])){
            $result['image_path'] = '';
            $result['image_type'] = '';
        	}else{
        		
                $image = $row1['image'];
                $ext = pathinfo($image, PATHINFO_EXTENSION);
        		$result['image_path'] = "http://dsdsr.com/msell-dsgroup-dms/myuploads/notification/".$row1['image'];
                $result['image_type'] = $ext;
        	}
            $final_result[] = $result;
        } 

    }
}
//pre($final_result);
echo $try = json_encode(array('result' => $final_result));

?>