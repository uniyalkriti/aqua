<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
class MyConfiguration extends Model
{
/// GET ALL JUNIORS FOR SENIOR
public function getJuniorUser($code){
        global $dbc;
        //static $data;
        $qry="";
        $res1="";
        $res2="";
$_SESSION['juniordata'][]= "'".$code."'";
	$details = DB::table('users_details')
		->where('senior_id',$code)
		->select('employee_code')->get();
		$num = count($details);
      
        if($num<=0){
            foreach($details as $key1=>$res1){
            if($res1->employee_code!=""){
                $_SESSION['juniordata'][]= "'".$res1->employee_code."'";
            }
        }
}
        else
        {

            foreach($details as $key=>$res2){
                if($res2->employee_code!=""){
                    $_SESSION['juniordata'][]= "'".$res2->employee_code."'";
                    $this->getJuniorUser($res2->employee_code);
                }
            }
        }
    }


 public function getSeniorUser($code){
        global $dbc;
        //static $data;
        $qry="";
        $res1="";
        $res2="";
//$_SESSION['seniordata'][]= "'".$code."'";
		
$details = DB::table('users_details')
		->where('employee_code',$code)
		->select('senior_id')->get();
		$num = count($details);
      
        if($num<=0){
            foreach($details as $key1=>$res1){
            if($res1->senior_id!=""){
                $_SESSION['seniordata'][]= "'".$res1->senior_id."'";
            }
        }
}
        else
        {

            foreach($details as $key=>$res2){
                if($res2->senior_id!=""){
                    $_SESSION['seniordata'][]= "'".$res2->senior_id."'";
                    $this->getSeniorUser($res2->senior_id);
                }
            }
        }
    }

 public static function fetchData($url='',$datastring='')
    {
        $ch=curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $datastring);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER,
                   array('Content-Type:application/json',
                          'Content-Length: ' . strlen($datastring))
                   );
       curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
    
        $result = curl_exec($ch);
        curl_close($ch);
       return $result;
    }

public static function SendFcmNotification($fcm_token, $msg)
    {
        $url= "https://fcm.googleapis.com/fcm/send";

        $fields = array(
            'to'=> $fcm_token,
            'notification' => $msg
        );
        $headers = array(
            'Authorization: key='.config('app.FCM_API_ACCESS_KEY'),
            'Content-Type: application/json'
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        $result = curl_exec($ch);
        curl_close($ch);
        if($result === FALSE)
        {
         die('Curl Failed: '.curl_error($ch));
        } 
        return $result;  
    }
    public static function gmtToIst($date)
    {
      //  echo $date; exit;
      $ist = date('Y-m-d H:i:s',strtotime('+330 minutes', strtotime($date))); 
      return $ist;
    }

    // NORMAL DATA SEND

    public static function curlSend($url,$arrmsg)
    {
       
        $curl_handle=curl_init();
        curl_setopt($curl_handle,CURLOPT_URL,$url);
        curl_setopt($curl_handle, CURLOPT_POST, 1);
        curl_setopt($curl_handle, CURLOPT_POSTFIELDS, json_encode($arrmsg));
        $res = curl_exec($curl_handle);
        curl_close($curl_handle);
       // print_r($res);
        if ($res) {
            echo "success message";
           
        }
      //  exit;
    }

    
}
