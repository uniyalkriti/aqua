<?php	

//TEST URL - http://localhost/haldiram/webservices/image_post.php
require_once('../admin/include/conectdb.php');
require_once 'functions.php';
error_reporting(0);
if(isset($_POST['response'])){$check=$_POST['response'];} else $check='';

// $check='{"response":{"AttendenceImage":[{"time":" 9:57:36","imei":"911533155418856","image_name":"2017-06-14-352824971.jpg","date":"2017-06-14","orderid":"20170614095736","image_source":"Attendence","user_id":"157","image":"\/9j\/4AAQSkZJRgABAQAAAQABAAD\/2wBDABsSFBcUERsXFhceHBsgKEIrKCUlKFE6PTBCYFVlZF9VXVtqeJmBanGQc1tdhbWGkJ6jq62rZ4C8ybqmx5moq6T\/2wBDARweHigjKE4rK06kbl1upKSkpKSkpKSkpKSkpKSkpKSkpKSkpKSkpKSkpKSkpKSkpKSkpKSkpKSkpKSkpKSkpKT\/wAARCADMAJkDASIAAhEBAxEB\/8QAHwAAAQUBAQEBAQEAAAAAAAAAAAECAwQFBgcICQoL\/8QAtRAAAgEDAwIEAwUFBAQAAAF9AQIDAAQRBRIhMUEGE1FhByJxFDKBkaEII0KxwRVS0fAkM2JyggkKFhcYGRolJicoKSo0NTY3ODk6Q0RFRkdISUpTVFVWV1hZWmNkZWZnaGlqc3R1dnd4eXqDhIWGh4iJipKTlJWWl5iZmqKjpKWmp6ipqrKztLW2t7i5usLDxMXGx8jJytLT1NXW19jZ2uHi4+Tl5ufo6erx8vP09fb3+Pn6\/8QAHwEAAwEBAQEBAQEBAQAAAAAAAAECAwQFBgcICQoL\/8QAtREAAgECBAQDBAcFBAQAAQJ3AAECAxEEBSExBhJBUQdhcRMiMoEIFEKRobHBCSMzUvAVYnLRChYkNOEl8RcYGRomJygpKjU2Nzg5OkNERUZHSElKU1RVVldYWVpjZGVmZ2hpanN0dXZ3eHl6goOEhYaHiImKkpOUlZaXmJmaoqOkpaanqKmqsrO0tba3uLm6wsPExcbHyMnK0tPU1dbX2Nna4uPk5ebn6Onq8vP09fb3+Pn6\/9oADAMBAAIRAxEAPwDYxRinYpKYCYpMU6jFIBuKKWjFACUmKXFGKAEpMU7FJQAmKMUtJQMTFJinGkoAYRTaeabigBuKei7OW4z0p8Ue489BUnlcEyHtzSbERM2O1M3\/AOzTTIGLBTnacH2pMt\/dpgXqKWkpgJSU6ikA2ilooASkp1JQAlJTqSgBKSnUhoAaaQ0pppoGIaFUscCjFWYI8DcepoAeiBVwKqX05AEcZ+Y9OM49\/wAP54qxcSiGMsTWeAQGllO0nk5PCj0\/z70gK88qWcBkPboM8sazP7auP+ecX5H\/ABold9VvdicRJ39B6\/Wr\/wDZtn\/zxP5mgRvUlOpKoBKSlooASilpKAEopaKAEpKWikAlNNOppoAaaaaU0KpY4HWgY+GPc2T0FWSQqknoKRFCrgVUupTI\/kocd2I7D\/6\/QfiaQiJ2NxLvONin5fc+v4f4+1ZGsXbSyCyt\/mJOHx6+lXNUvVsrfZHgSMMIAOg9ah0awMa\/aZhmRxlc9QP8TQMsWNmtpbhMAueWYdzVnb9aceBmk+b+6aAL9JS0lUISilpKAEopaSgApKWikAlJTqSgBppppxppoAbViGPaNx6mmRJubJ6CppHEaFjjj1oAhuZvLTgEs3AA71TllSzt3llbPcn1PoP5VIpMjGZ+P7uew9fx\/l+NYdxI+sXwghJECc5\/mf8ACgAsIJNSvGu7kZjU8Ke\/t9BW90pkUSQxrHGMKowKkRPMbHYdaQwii8xw7E7R0HrVraKUAKvsKTcf7ppgFFFFMQlJS0UAJRRRSAKKKKAEopaQ0AMNAUk4FLipokwMnqaAFVQi+wqlM\/2mUoMeWn3vc+n+P4D1qW7mORDGfnbv6e\/4f4VQ1G7TTrQBOZG4QH17k0AVNZvHdxY22TI\/D4\/lVzT7JbKAIMFzy7Duar6PYGJTc3AJnk5G7qAf6mtI+g6mgBACzbV61bjQIuBTYYti89T1p\/U47d6ADrz2paKKAG0lLSUwCkoopALSUUUAFFFFABRRRQAKBuGaW4mEMfX5j04zQBmkljUguxJYHI\/woApu6WsElxMccZPt6D\/Pc1l2ED6ldtfXI\/dg4RT0\/wD1CpdQgub++jgKNHbLyW9ff61qRxrFGqIoVVGABQApqSCIgl26noPSiKPcdx6dqnPFACH0FKBgYoAxRQAUlLRQBHSUlFMBaKTNGaQC0UmaKAFopKKAFpaSnKO9ACj5Rk04LkfMOvakUbjntT6AIJYwoLA8DtSLGWbB4A60bDLcCQ52Jwg7E92\/oKnAwMUAGMCkpaKACkpaKAEooooAgzSZpM0ZoAdmim5ozQA6im5paAFpaSgUAOAzTLq4WCPcRn29apahq8VkfLVfNl7qDjH1rCvdWnuU2kKo9AuKALd3r1yrkQyYweyjb9Ocn8c06LxLcM2JYY2U\/wB0lT\/WsEtSZpgdpa6tFKm6RAi5ALA5C+meBj8MitKuCtL2W1dShBAPQjPXqK6rRLyK4gMcbHCdFZslRnp\/L8\/agDTpKKKQBRRRQAlFFFAFTNGabS5oGLmlptITigB4pajB5704NikA+oNRuTaWMk4xuAwv1NTgZrL8SgtYRhf+eoH6GmBzo825lwAWdjk1eGjy7R5jYPpWno1gttB5smDI\/P0FW5ualspIwP7JIP3+KR9LAHBJNa3JNRScA5NK7K5UYM0DxGp9LvDZXkc3JUcMB3FXZUDjBFZk0Ril29jVJkNHfKwZQynIIyCO9LUVr\/x6wj\/YH8qlpkhSUtJQAUUUUAU6QinUEUhjM4Gaac96c4+Q\/Skc\/LQA0uF60hck5BpjEkEGmIT0INOSsTF3L8R3LxVTWgDaKOOHzz7AmrMBAXr2qG+UTWsi4yccfWgorK1zLaIy7YxtyO5P+FZstxdI+0yKfwrXWRVgVGdY3VcFWODWLJGpmbLjrxzRZFAbydcqVBbGRzUQmll5Z9o9K0ra2BR52XtsXIxwO\/5\/yrPlj2SlccdV44+lSrXB3HBX6+cc+lNZHkliDhSC23I4qNVUHiRR\/wACFXLdDJJGoOQpDFiOP\/r96YjqlAVQB0AxS01TuUH1GaWmSFFFFABSUtFAFWkpRRSGJ1qI5Ax6VLSFQx96AKpHzelToARyKRosc4qRVwOfyoAjlIijLjgDtSNIkYDNn5wABTp4\/MXZnGSM1HO2JkYj5VDfyoew1uWJVDrzVCQLE4CAAk1beT5azLi4WNuQXc8BRUlosy3kHzxqwLKeazHlilZtjDKnkVHJFcMxcgKndQaqAvDJlhuUelOwrmmFHWrEGN1Uop1kX5TmrMDAOCaQM0NJu5ZZXilYt8odSa1KwtCy1yWPZCP1FblWiGLRRSigQYoxS0UAUMY6Gl3evFIKWkMWlWmYx0pwzj0oARjTxzzUeMsBnipRQAmzc3NJcxb7chQMipQKcORigDHWbdGPXFZIZ2mzglmOBVxJFimdHOCmRVOC7VZnI4yeKRVy3JbzhQGK9M4zVGVHTO4Aj2p9zqEobpmq32h5PmPAoHdDUJjlBHQ9qtrOV5qlJIN4PWlaUHgUyTptCUFXk7lVFa1VdOjSOyhKKFLRqWIHU4qzQJi0CkoBpiHiilXBFLQBnCnUwUtIY6nfw0wU5ulACL96pRUSfeNSigBwpwpopRQBzGvQmC+Lrwso3Z9+\/wDn3rK6Nmuq12FJbUlhyqkg+lcmCcUwLW9XADHgdajmKqmE6VDk0E5FKwDKVeTSUqd6YGu91dRxqwmkMRAxhjx7UkV5Jnhmyfen6d89qobkYxTJo1XJUYwe1MRftr24xy+SPU1OLyZgdzED2rMt2O7rVyMkFvamInE75wJn\/wC+qk+0v\/z2f86qSEgjHen4oA\/\/2Q=="}],"Dailyexpense":[],"Retailer":[],"Complaint":[],"CallWiseReportingImage":[],"Merchandise":[]}}';

global $dbc;
$data=json_decode($check);
$attendance_image=$data->response->AttendenceImage;
$complaint_image=$data->response->Complaint;
$retailer_image=$data->response->Retailer;
$daily_expense=$data->response->Dailyexpense;
$sale_image=$data->response->CallWiseReportingImage;
$merchandise=$data->response->Merchandise;
if($data)
{
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
    if(!empty($attendance_image)){
    //echo'<pre>';
    //print_r($attendance_image);
    //echo '</pre>';
       $a=0;
        $count=count($attendance_image);
        while($a<$count){
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
            fwrite($file, $binary);

            if(fwrite($file, $binary))
            {        
                echo 'Y';
            }
            else{
                echo 'N';
            }
            fclose($file);
            $up = "UPDATE `user_daily_attendance` SET `image_name`='$image_name' WHERE order_id='$orderid$user_id'";
            //$up = "UPDATE `user_daily_attendance` SET `image_name`='$image_name' WHERE DATE_FORMAT(`work_date`,'%Y-%m-%d')='$date' AND user_id='$user_id'";
             $uprun=  mysqli_query($dbc, $up);
            $a++;
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
        $up = "UPDATE `user_expense_report` SET `image_name1`='$image_name1',`image_name2`='$image_name2',`image_name3`='$image_name3' WHERE order_id='$orderid$user_id'";
        $uprun=  mysqli_query($dbc, $up);
        $n++;
    }
	}
   //  echo $res;

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

else
    {
    echo 'N';
    }

?>