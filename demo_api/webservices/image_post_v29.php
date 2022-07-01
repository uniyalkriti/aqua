<?php
//TEST URL - http://localhost/haldiram/webservices/image_post.php
require_once('../admin/include/conectdb.php');
require_once 'functions.php';
error_reporting(0);
if(isset($_POST['response'])){$check=$_POST['response'];} else $check='';

 //$check='';

global $dbc;
$data=json_decode($check);
$attendance_image=$data->response->AttendenceImage;
$complaint_image=$data->response->Complaint;
$retailer_image=$data->response->Retailer;
$daily_expense=$data->response->Dailyexpense;
$sale_image=$data->response->CallWiseReportingImage;
$merchandise=$data->response->Merchandise;
$merchandise_requirement=$data->response->MerchandiseCheck;
$profile_image=$data->response->profileImage;
$dealer_damge_image=$data->response->DealerDamageImage;
$retailer_damge_image=$data->response->RetailerDamageImage;
$market_report_1=$data->response->MarketImage1;
$market_report_2=$data->response->MarketImage2;
// print_r($data);
if($data)
{
    // print_r($attendance_image);
    // starts here 
    if(!empty($dealer_damge_image))
    {

        $der=0;
        $der_count=count($dealer_damge_image);
        while($der<$der_count)
        {
            $orderid=$dealer_damge_image[$der]->orderid;
            $time=$dealer_damge_image[$der]->time;
            $date=$dealer_damge_image[$der]->date;
            $image_name=$dealer_damge_image[$der]->image_name;
            $image_source=$dealer_damge_image[$der]->image_source;
            $imagewww = $dealer_damge_image[$der]->image;
            $path='mobile_images/Dealer_Damage_Image/';
            $image=str_replace("\/",'/', $imagewww);
            $binary=base64_decode($image);
            header('Content-Type: bitmap; charset=utf-8');
            $file = fopen($path.$image_name, 'wb');
            fwrite($file, $binary);

            if(fwrite($file, $binary))
            {
                echo 'Y';
            }
            else{
                echo 'N';
            }
            fclose($file);
            $upmer = "UPDATE `damage_replace` SET `image`='$image_name' WHERE replaceid='$orderid'"; 
            $upmerrun=  mysqli_query($dbc, $upmer);
            $mer++;
        }
    }
    if(!empty($retailer_damge_image))
    {

        $retailer=0;
        $reta_count=count($retailer_damge_image);
        while($retailer<$reta_count)
        {
            $orderid=$retailer_damge_image[$retailer]->orderid;
            $time=$retailer_damge_image[$retailer]->time;
            $date=$retailer_damge_image[$retailer]->date;
            $image_name=$retailer_damge_image[$retailer]->image_name;
            $image_source=$retailer_damge_image[$retailer]->image_source;
            $imagewww = $retailer_damge_image[$retailer]->image;
            $path='mobile_images/Retailer_Damage_Image/';
            $image=str_replace("\/",'/', $imagewww);
            $binary=base64_decode($image);
            header('Content-Type: bitmap; charset=utf-8');
            $file = fopen($path.$image_name, 'wb');
            fwrite($file, $binary);

            if(fwrite($file, $binary))
            {
                echo 'Y';
            }
            else{
                echo 'N';
            }
            fclose($file);
            $upmer = "UPDATE `damage_replace_retailer` SET `image`='$image_name' WHERE replaceid='$orderid'"; 
             $upmerrun=  mysqli_query($dbc, $upmer);
            $mer++;
        }
    }

    if(!empty($market_report_1))
    {

        $inc=0;
        $market_1=count($market_report_1);
        while($inc<$market_1)
        {
            $orderid=$market_report_1[$inc]->orderid;
            $time=$market_report_1[$inc]->time;
            $date=$market_report_1[$inc]->date;
            $image_name=$market_report_1[$inc]->image_name;
            $image_source=$market_report_1[$inc]->image_source;
            $imagewww = $market_report_1[$inc]->image;
            $path='mobile_images/marketReport1/';
            $image=str_replace("\/",'/', $imagewww);
            $binary=base64_decode($image);
            header('Content-Type: bitmap; charset=utf-8');
            $file = fopen($path.$image_name, 'wb');
            fwrite($file, $binary);

            if(fwrite($file, $binary))
            {
                echo 'Y';
            }
            else{
                echo 'N';
            }
            fclose($file);
            $upmer = "UPDATE `market_report_1` SET `image_name`='$image_name' WHERE orderid='$orderid'"; 
             $upmerrun=  mysqli_query($dbc, $upmer);
            $mer++;
        }
    }
    if(!empty($market_report_2))
    {

        $inc=0;
        $market_2=count($market_report_2);
        while($inc<$market_2)
        {
            $orderid=$market_report_2[$inc]->orderid;
            $time=$market_report_2[$inc]->time;
            $date=$market_report_2[$inc]->date;
            $image_name=$market_report_2[$inc]->image_name;
            $image_source=$market_report_2[$inc]->image_source;
            $imagewww = $market_report_2[$inc]->image;
            $path='mobile_images/marketReport2/';
            $image=str_replace("\/",'/', $imagewww);
            $binary=base64_decode($image);
            header('Content-Type: bitmap; charset=utf-8');
            $file = fopen($path.$image_name, 'wb');
            fwrite($file, $binary);

            if(fwrite($file, $binary))
            {
                echo 'Y';
            }
            else{
                echo 'N';
            }
            fclose($file);
            $upmer = "UPDATE `market_report_2` SET `image_name`='$image_name' WHERE orderid='$orderid'"; 
             $upmerrun=  mysqli_query($dbc, $upmer);
            $mer++;
        }
    }
    //  ends here 
    if(!empty($merchandise)){

       $mer=0;
        $mer_count=count($merchandise);
        while($mer<$mer_count){
            $orderid=$merchandise[$mer]->orderid;
            $time=$merchandise[$mer]->time;
            $date=$merchandise[$mer]->date;
            $image_name=$merchandise[$mer]->image_name;
            $image_source=$merchandise[$mer]->image_source;
    		$imagewww = $merchandise[$mer]->image;
            $path='mobile_images/Merchandise/';
            $image=str_replace("\/",'/', $imagewww);
            $binary=base64_decode($image);
            header('Content-Type: bitmap; charset=utf-8');
            $file = fopen($path.$image_name, 'wb');
            fwrite($file, $binary);

            if(fwrite($file, $binary))
            {
                echo 'Y';
            }
            else{
                echo 'N';
            }
            fclose($file);
            $upmer = "UPDATE `merchandise` SET `image`='$image_name' WHERE order_id='$orderid'";
             $upmerrun=  mysqli_query($dbc, $upmer);
            $mer++;
        }


}

if(!empty($merchandise_requirement)){

       $merreq=0;
        $merreq_count=count($merchandise_requirement);
        while($merreq<$merreq_count){
            $orderid=$merchandise_requirement[$merreq]->orderid;
            $time=$merchandise_requirement[$merreq]->time;
            $date=$merchandise_requirement[$merreq]->date;
            $image_name=$merchandise_requirement[$merreq]->image_name;
            $image_source=$merchandise_requirement[$merreq]->image_source;
    		$imagewww = $merchandise_requirement[$merreq]->image;
            $path='mobile_images/MerchandiseRequirement/';
            $image=str_replace("\/",'/', $imagewww);
            $binary=base64_decode($image);
            header('Content-Type: bitmap; charset=utf-8');
            $file = fopen($path.$image_name, 'wb');
            fwrite($file, $binary);

            if(fwrite($file, $binary))
            {
                echo 'Y';
            }
            else{
                echo 'N';
            }
            fclose($file);
            $upmerq = "UPDATE `merchandise_requirement` SET `image`='$image_name' WHERE order_id='$orderid'";
             $upmerrunq=  mysqli_query($dbc, $upmerq);
            $merreq++;
        }

}
// print_r($attendance_image);

    if(!empty($attendance_image)){
       $a=0;
        $count=count($attendance_image);
        while($a<$count)
        {
            // print_r($attendance_image); die;
            $image=$attendance_image[$a]->image;
            $time=$attendance_image[$a]->time;
            $orderid=$attendance_image[$a]->orderid;
            $date=$attendance_image[$a]->date;
            $user_id=$attendance_image[$a]->user_id;
            $image_name=$attendance_image[$a]->image_name;
            $image_source=$attendance_image[$a]->image_source;
            $path='mobile_images/Attendance/';
            $binary=base64_decode($image);
            header('Content-Type: bitmap; charset=utf-8');
            $file = fopen($path.$image_name, 'wb');
    		//echo $image_name."FILE = ".$file;
            $check=fwrite($file, $binary);

            if(!empty($check))
            {
                $res= 'Y';
            }
            else{
                $res= 'N';
            }
            fclose($file);
            $up = "UPDATE `user_daily_attendance` SET `image_name`='$image_name' WHERE order_id='$orderid$user_id'";
            //$up = "UPDATE `user_daily_attendance` SET `image_name`='$image_name' WHERE DATE_FORMAT(`work_date`,'%Y-%m-%d')='$date' AND user_id='$user_id'";
             $uprun=  mysqli_query($dbc, $up);
            $a++;
            echo $res;
        }


}
    if(!empty($profile_image)){
       $a=0;
        $count=count($profile_image);
        while($a<$count){
            $image=$profile_image[$a]->image;
            $time=$profile_image[$a]->time;
            $orderid=$profile_image[$a]->orderid;
            $date=$profile_image[$a]->date;
            $user_id=$profile_image[$a]->user_id;
            $image_name=$profile_image[$a]->image_name;
            $image_name=date('YmdHis').$user_id.".png";
            $image_source=$profile_image[$a]->image_source;
            $path='mobile_images/profile/';
            $binary=base64_decode($image);
            header('Content-Type: bitmap; charset=utf-8');
            $file = fopen($path.$image_name, 'wb');
    		//echo $image_name."FILE = ".$file;
            $check=fwrite($file, $binary);

            if(!empty($check))
            {
                $res= 'Y';
            }
            else{
                $res= 'N';
            }
            fclose($file);
            $upp = "UPDATE `person_login` SET `person_image`='$image_name' WHERE person_id='$user_id'";
            //$up = "UPDATE `user_daily_attendance` SET `image_name`='$image_name' WHERE DATE_FORMAT(`work_date`,'%Y-%m-%d')='$date' AND user_id='$user_id'";
             $upprun=  mysqli_query($dbc, $upp);
            $a++;
            echo $res;
        }


}

    if(!empty($complaint_image)){
    //echo'<pre>';
    //print_r($complaint_image);
    //echo '</pre>';
        $c=0;
        $count=count($complaint_image);
        while($c<$count){
            $image=$complaint_image[$c]->image;
            $user_id=$complaint_image[$c]->user_id;
            $order_id=$complaint_image[$c]->order_id;
            $image_name=$complaint_image[$c]->image_name;
            $date_time=$complaint_image[$c]->date_time;
            $image_source=$complaint_image[$c]->image_source;
            $path='mobile_images/Complaint/';
            $binary=base64_decode($image);
            header('Content-Type: bitmap; charset=utf-8');
            $file = fopen($path.$image_name, 'wb');
            fwrite($file, $binary);

            if(fwrite($file, $binary))
            {
                echo 'Y';
            }
            else{
                echo 'N';
            }
            fclose($file);
            $up = "UPDATE `user_complaint` SET `image_name` ='$image_name' WHERE order_id='$order_id$user_id'";
             $uprun=  mysqli_query($dbc, $up);
            $c++;
        }


}
    if(!empty($retailer_image)){
    //echo'<pre>';
    //print_r($retailer_image);
    //echo '</pre>';
        $r=0;
        $count=count($retailer_image);
        while($r<$count){
            $image=$retailer_image[$r]->image;
            $time=$retailer_image[$r]->time;
            $date=$retailer_image[$r]->date;
            $retailer_id=$retailer_image[$r]->retailer_id;
            $image_name=$retailer_image[$r]->image_name;
            $image_source=$retailer_image[$r]->image_source;
            $path='mobile_images/Retailer/';
            $binary=base64_decode($image);
            header('Content-Type: bitmap; charset=utf-8');
            $file = fopen($path.$image_name, 'wb');
            fwrite($file, $binary);

            if(fwrite($file, $binary))
            {
                echo 'Y';
            }
            else{
                echo 'N';
            }
            fclose($file);
            $up = "UPDATE `retailer` SET `image_name` ='$image_name' WHERE id='$retailer_id'";
            $uprun=  mysqli_query($dbc, $up);
            $r++;
        }


}
}
if(!empty($daily_expense)){
//echo'<pre>';
//print_r($daily_expense);
//echo '</pre>';
    $n=0;
    $countdaily_expense=count($daily_expense);
    while($n<$countdaily_expense){
        $image1=$daily_expense[$n]->image1;
        $image2=$daily_expense[$n]->image2;
        $image3=$daily_expense[$n]->image3;
        $image_name1=$daily_expense[$n]->image_name1;
        $image_name2=$daily_expense[$n]->image_name2;
        $image_name3=$daily_expense[$n]->image_name3;
        $time=$daily_expense[$n]->time;
        $date=$daily_expense[$n]->date;
        $user_id=$daily_expense[$n]->user_id;
        $orderid=$daily_expense[$n]->orderid;
        $image_source=$daily_expense[$n]->image_source;
        $path='mobile_images/Expense/';
        $binary1=base64_decode($image1);
        $binary2=base64_decode($image2);
        $binary3=base64_decode($image3);
        if(!empty($image1))
        {
        header('Content-Type: bitmap; charset=utf-8');
        $file1 = fopen($path.$image_name1, 'wb');
        fwrite($file1, $binary1);

        if(fwrite($file1, $binary1))
        {
            echo 'Y';
        }
        else{
            echo 'N';
        }
        fclose($file1);
        }
         if(!empty($image2))
        {
        //image 2
        header('Content-Type: bitmap; charset=utf-8');
        $file2 = fopen($path.$image_name2, 'wb');
        fwrite($file2, $binary2);

        if(fwrite($file2, $binary2))
        {
            echo 'Y';
        }
        else{
            echo 'N';
        }
        fclose($file2);
        }
         if(!empty($image3))
        {
        //image 3
        header('Content-Type: bitmap; charset=utf-8');
        $file3 = fopen($path.$image_name3, 'wb');
        fwrite($file3, $binary3);

        if(fwrite($file3, $binary3))
        {
            echo 'Y';
        }
        else{
            echo 'N';
        }
        fclose($file3);
        $up = "UPDATE `travelling_expense_bill` SET `image_name1`='$image_name1',`image_name2`='$image_name2',`image_name3`='$image_name3' WHERE order_id='$orderid'";
        $uprun=  mysqli_query($dbc, $up);
        $n++;
    }
    }

}

if(!empty($sale_image)){
//echo'<pre>';
//print_r($attendance_image);
//echo '</pre>';
   $s=0;
    $count=count($sale_image);
    while($s<$count){
        $image=$sale_image[$s]->image;
        $user_id=$sale_image[$s]->user_id;
        $orderid=$sale_image[$s]->orderid;
        $image_name=$sale_image[$s]->image_name;
        $image_source=$sale_image[$s]->image_source;
        $time=$sale_image[$s]->time;
        $date=$sale_image[$s]->date;
        $path='mobile_images/sale/';
        $binary=base64_decode($image);
        header('Content-Type: bitmap; charset=utf-8');
        $file = fopen($path.$image_name, 'wb');
        fwrite($file, $binary);

        if(fwrite($file, $binary))
        {
             echo 'Y';
        }
        else{
             echo 'N';
        }
        fclose($file);
        $up = " UPDATE `user_sales_order` SET `image_name`= '$image_name' WHERE order_id='$orderid$user_id'";
         $uprun=  mysqli_query($dbc, $up);
        $s++;
    }

}

//else
//    {
//    echo 'N77777';
//    }

?>