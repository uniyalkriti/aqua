<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;


class AddressUpdationController extends Controller
{
    public function AddressUpdateData(Request $requuest)
    {
        $date = date('Y-m-d');
        $count = 0;
        
        return view('AddressUpdate.index', [

            'date' => $date,
            'count' => $count

        ]);
    }

    #show Address Updation Option 
    public function ShowAddressUpdationOption(Request $request)
    {
       
        if(!empty($request->from_date)){
            $date = date('Y-m-d', strtotime($request->from_date));
        }
        else{
            $date = date('Y-m-d');
        }
    //  dd($date);

        return view('AddressUpdate.ajax', [
            'date' => $date
          
        ]);
    }
  ##...................... function for attendance address update ....................##
    public function UpdateAttendanceAddress(Request $request)
    {
       
        if(!empty($request->from_date)){
            $date = date('Y-m-d', strtotime($request->from_date));
        }
        else{
            $date = date('Y-m-d');
        }
    //  dd($date);

    $q = DB::table('user_daily_attendance')
    ->whereRaw("DATE_FORMAT(work_date, '%Y-%m-%d') = '$date'")
    ->get();
// dd($q);
    $count = 0;

 foreach($q as $a => $b){
   
    $lat_lng1 = explode(',', $b->lat_lng);
    $lat = !empty($lat_lng1[0])?$lat_lng1[0]:'0.0';
    $lng = !empty($lat_lng1[1])?$lat_lng1[1]:'0.0';
  
    
    if ($b->track_addrs == "" || $b->track_addrs == ", , , " ) {

        if ($b->lat_lng == "" || $lat == "0.0" || $lng == "0.0") {
            if ($b->mnc_mcc_lat_cellid != "0:0:0:0" || $b->mnc_mcc_lat_cellid != "" || $b->mnc_mcc_lat_cellid != "false") {

              
                $mmlc = explode(':', $b->mnc_mcc_lat_cellid);
               
                $mnc = !empty($mmlc[0])?$mmlc[0]:0;
                $mcc = !empty($mmlc[1])?$mmlc[1]:0;
                $lac = !empty($mmlc[2])?$mmlc[2]:0;
                $cellid = !empty($mmlc[3])?$mmlc[3]:0;
         
                
                $latlng = $this->getlatlongbymccmnclaccid($mnc, $mcc, $lac, $cellid);
                $lat_lng = explode(",", $latlng);
                $lat = $lat_lng[0];
                $lng = $lat_lng[1];
                $final = $lat . ',' . $lng;
                $url = 'https://maps.googleapis.com/maps/api/geocode/json?key=AIzaSyBMeqXeiH3H2aBSKe-EsSi-dIL7XY8MAhI&callback=initMap&latlng='.trim($lat).','.trim($lng).'&sensor=false';
              
                $json = @file_get_contents($url);
                
                $data=json_decode($json);
                // dd($data);
                $status = $data->status;
                if($status=="OK")
                {
                  $address = $data->results[0]->formatted_address;
                }
                else
                {
                 $address = '';
                 }

                 $myArr2=[
                    'track_addrs'=>$address,
                    'lat_lng'=>$final,
                  
                ];
         
                 $q = DB::table('user_daily_attendance')
                      ->where('id',$b->id)  
                      ->update($myArr2);

                      $count++;
                
                    //   dd($q);
            }
        }
        else {
            $address = $this->getLocationByLatLng($lat, $lng);
            $final = $lat . ',' . $lng;
            
            $myArr2=[
                'track_addrs'=>$address,
                'lat_lng'=>$final,
              
            ];
     
             $q = DB::table('user_daily_attendance')
                  ->where('id',$b->id)  
                  ->update($myArr2);

             $count++;
         }
    }
 }
        return view('AddressUpdate.index', [
            'date' => $date,
            'count' => $count

          
        ]);
    }

##...............................function for attendance update address ends here ....................##


##.............................function for checkOut address update ...........................##
public function UpdateCheckoutAddress(Request $request)
{
   
    if(!empty($request->from_date)){
        $date = date('Y-m-d', strtotime($request->from_date));
    }
    else{
        $date = date('Y-m-d');
    }
//  dd($date);

$q = DB::table('check_out')
->whereRaw("DATE_FORMAT(work_date, '%Y-%m-%d') = '$date'")
->get();
// dd($q);
$count = 0;

foreach($q as $a => $b){

$lat_lng1 = explode(',', $b->lat_lng);
$lat = !empty($lat_lng1[0])?$lat_lng1[0]:'0.0';
$lng = !empty($lat_lng1[1])?$lat_lng1[1]:'0.0';


if ($b->attn_address == "" || $b->attn_address == ", , , " ) {

    if ($b->lat_lng == "" || $lat == "0.0" || $lng == "0.0") {
        if ($b->mnc_mcc_lat_cellid != "0:0:0:0" || $b->mnc_mcc_lat_cellid != "" || $b->mnc_mcc_lat_cellid != "false") {

          
            $mmlc = explode(':', $b->mnc_mcc_lat_cellid);
           
            $mnc = !empty($mmlc[0])?$mmlc[0]:0;
            $mcc = !empty($mmlc[1])?$mmlc[1]:0;
            $lac = !empty($mmlc[2])?$mmlc[2]:0;
            $cellid = !empty($mmlc[3])?$mmlc[3]:0;
     
            
            $latlng = $this->getlatlongbymccmnclaccid($mnc, $mcc, $lac, $cellid);
            $lat_lng = explode(",", $latlng);
            $lat = $lat_lng[0];
            $lng = $lat_lng[1];
            $final = $lat . ',' . $lng;
            $url = 'https://maps.googleapis.com/maps/api/geocode/json?key=AIzaSyBMeqXeiH3H2aBSKe-EsSi-dIL7XY8MAhI&callback=initMap&latlng='.trim($lat).','.trim($lng).'&sensor=false';
          
            $json = @file_get_contents($url);
            
            $data=json_decode($json);
            // dd($data);
            $status = $data->status;
            if($status=="OK")
            {
              $address = $data->results[0]->formatted_address;
            }
            else
            {
             $address = '';
             }

             $myArr2=[
                'attn_address'=>$address,
                'lat_lng'=>$final,
              
            ];
     
             $q = DB::table('check_out')
                  ->where('id',$b->id)  
                  ->update($myArr2);

                  $count++;
            
                //   dd($q);
        }
    }
    else {
        $address = $this->getLocationByLatLng($lat, $lng);
        $final = $lat . ',' . $lng;
        
        $myArr2=[
            'attn_address'=>$address,
            'lat_lng'=>$final,
          
        ];
 
         $q = DB::table('check_out')
              ->where('id',$b->id)  
              ->update($myArr2);

         $count++;
     }
}
}
    return view('AddressUpdate.index', [
        'date' => $date,
        'count' => $count
      
    ]);
}
##.............................function for checkOut address update ends here ...................##

##.............................function for DailyTracking address update  ...................##

public function UpdateDailyTrackingAddress(Request $request)
{
   
    if(!empty($request->from_date)){
        $date = date('Y-m-d', strtotime($request->from_date));
    }
    else{
        $date = date('Y-m-d');
    }
//  dd($date);

$q = DB::table('user_daily_tracking')
->whereRaw("DATE_FORMAT(track_date, '%Y-%m-%d') = '$date'")
->get();
// dd($q);
$count = 0;

foreach($q as $a => $b){

$lat_lng1 = explode(',', $b->lat_lng);
$lat = !empty($lat_lng1[0])?$lat_lng1[0]:'0.0';
$lng = !empty($lat_lng1[1])?$lat_lng1[1]:'0.0';


if ($b->track_address == "" || $b->track_address == ", , , " ) {

    if ($b->lat_lng == "" || $lat == "0.0" || $lng == "0.0") {
        if ($b->mnc_mcc_lat_cellid != "0:0:0:0" || $b->mnc_mcc_lat_cellid != "" || $b->mnc_mcc_lat_cellid != "false") {

          
            $mmlc = explode(':', $b->mnc_mcc_lat_cellid);
           
            $mnc = !empty($mmlc[0])?$mmlc[0]:0;
            $mcc = !empty($mmlc[1])?$mmlc[1]:0;
            $lac = !empty($mmlc[2])?$mmlc[2]:0;
            $cellid = !empty($mmlc[3])?$mmlc[3]:0;
     
            
            $latlng = $this->getlatlongbymccmnclaccid($mnc, $mcc, $lac, $cellid);
            $lat_lng = explode(",", $latlng);
            $lat = $lat_lng[0];
            $lng = $lat_lng[1];
            $final = $lat . ',' . $lng;
            $url = 'https://maps.googleapis.com/maps/api/geocode/json?key=AIzaSyBMeqXeiH3H2aBSKe-EsSi-dIL7XY8MAhI&callback=initMap&latlng='.trim($lat).','.trim($lng).'&sensor=false';
          
            $json = @file_get_contents($url);
            
            $data=json_decode($json);
            // dd($data);
            $status = $data->status;
            if($status=="OK")
            {
              $address = $data->results[0]->formatted_address;
            }
            else
            {
             $address = '';
             }

             $myArr2=[
                'track_address'=>$address,
                'lat_lng'=>$final,
              
            ];
     
             $q = DB::table('user_daily_tracking')
                  ->where('id',$b->id)  
                  ->update($myArr2);

                  $count++;
            
                //   dd($q);
        }
    }
    else {
        $address = $this->getLocationByLatLng($lat, $lng);
        $final = $lat . ',' . $lng;
        
        $myArr2=[
            'track_address'=>$address,
            'lat_lng'=>$final,
          
        ];
 
         $q = DB::table('user_daily_tracking')
              ->where('id',$b->id)  
              ->update($myArr2);

         $count++;
     }
}
}
    return view('AddressUpdate.index', [
        'date' => $date,
        'count' => $count
      
    ]);
}
##.............................function for DailyTracking address update ends here ...................##

##.............................function for convert mcc_mnc_lac_cid into lat_lng ...................##

    public function getlatlongbymccmnclaccid($mcc,$mnc,$lac,$cid){
    
        if(($mcc!='0') && ($mnc!='0')){
    $data = 
    "\x00\x0e". 
    "\x00\x00\x00\x00\x00\x00\x00\x00". 
    "\x00\x00". 
    "\x00\x00". 
    "\x00\x00". 
    "\x1b". 
    "\x00\x00\x00\x00". 
    "\x00\x00\x00\x00". 
    "\x00\x00\x00\x03".
    "\x00\x00".
    "\x00\x00\x00\x00". 
    "\x00\x00\x00\x00". 
    "\x00\x00\x00\x00". 
    "\x00\x00\x00\x00". 
    "\xff\xff\xff\xff". 
    "\x00\x00\x00\x00"  
    ;
      $mcc = substr("00000000".dechex($mcc),-8);
      $mnc = substr("00000000".dechex($mnc),-8);
      $lac = substr("00000000".dechex($lac),-8);
      $cid = substr("00000000".dechex($cid),-8);
    
    
    $init_pos = strlen($data);
    $data[$init_pos - 38]= pack("H*",substr($mnc,0,2));
    $data[$init_pos - 37]= pack("H*",substr($mnc,2,2));
    $data[$init_pos - 36]= pack("H*",substr($mnc,4,2));
    $data[$init_pos - 35]= pack("H*",substr($mnc,6,2));
    $data[$init_pos - 34]= pack("H*",substr($mcc,0,2));
    $data[$init_pos - 33]= pack("H*",substr($mcc,2,2));
    $data[$init_pos - 32]= pack("H*",substr($mcc,4,2));
    $data[$init_pos - 31]= pack("H*",substr($mcc,6,2));
    $data[$init_pos - 24]= pack("H*",substr($cid,0,2));
    $data[$init_pos - 23]= pack("H*",substr($cid,2,2));
    $data[$init_pos - 22]= pack("H*",substr($cid,4,2));
    $data[$init_pos - 21]= pack("H*",substr($cid,6,2));
    $data[$init_pos - 20]= pack("H*",substr($lac,0,2));
    $data[$init_pos - 19]= pack("H*",substr($lac,2,2));
    $data[$init_pos - 18]= pack("H*",substr($lac,4,2));
    $data[$init_pos - 17]= pack("H*",substr($lac,6,2));
    $data[$init_pos - 16]= pack("H*",substr($mnc,0,2));
    $data[$init_pos - 15]= pack("H*",substr($mnc,2,2));
    $data[$init_pos - 14]= pack("H*",substr($mnc,4,2));
    $data[$init_pos - 13]= pack("H*",substr($mnc,6,2));
    $data[$init_pos - 12]= pack("H*",substr($mcc,0,2));
    $data[$init_pos - 11]= pack("H*",substr($mcc,2,2));
    $data[$init_pos - 10]= pack("H*",substr($mcc,4,2));
    $data[$init_pos - 9]= pack("H*",substr($mcc,6,2));
    
    if ((hexdec($cid) > 0xffff) && ($mcc != "00000000") && ($mnc != "00000000")) {
      $data[$init_pos - 27] = chr(5);
    } else {
      $data[$init_pos - 24]= chr(0);
      $data[$init_pos - 23]= chr(0);
    }
    
    $context = array (
            'http' => array (
                'method' => 'POST',
                'header'=> "Content-type: application/binary\r\n"
                    . "Content-Length: " . strlen($data) . "\r\n",
                'content' => $data
                )
            );
          
    $xcontext = stream_context_create($context);
    
    $str=file_get_contents("http://www.google.com/glm/mmap",FALSE,$xcontext);
    
    if (strlen($str) > 10) {
      $lat_tmp = unpack("l",$str[10].$str[9].$str[8].$str[7]);
      $lon_tmp = unpack("l",$str[14].$str[13].$str[12].$str[11]);
    //    dd($lat_tmp);
      $lon = $lon_tmp[1]/1000000;
      $lat = $lat_tmp[1]/1000000;
      $ll=$lat.",".$lon;
    //   dd($ll);
    return $ll;
    
      }
       else{
        return $ll="0,0";   	
          }
    //http://yourwebhost.com/locate.php?mcc=XXX&mnc=XXX&lac=XXXXX&cid=XXXXXXXX
    
    //http://manacledemo.in/test5.php?mcc=405&mnc=5&lac=5081&cid=10912
        }else{
            return $ll="0,0";   	
        }
    }

##.............................function for convert mcc_mnc_lac_cid into lat_lng ends here ...................##


## ..................code to get address according to latitude , longitude........................##
public function getLocationByLatLng($lat,$lng){
    if(($lat!='0') && ($lng!='0')){
//http://maps.googleapis.com/maps/api/geocode/json?latlng=28.4023003,77.3229817&sensor=true 28.531368627418,77.2377156544599
    $data = file_get_contents('https://maps.googleapis.com/maps/api/geocode/json?key=AIzaSyBMeqXeiH3H2aBSKe-EsSi-dIL7XY8MAhI&callback=initMap&latlng='.$lat.','.$lng.'&sensor=true');

    $data1=json_decode($data, true);
  
	$address=!empty($data1['results'][0]['address_components'][1]['long_name'])?$data1['results'][0]['address_components'][1]['long_name'].' ':' N/A'." , ";
	$address.=!empty($data1['results'][0]['address_components'][2]['long_name'])?$data1['results'][0]['address_components'][2]['long_name'].' ':' N/A'." , ";
	$address.=!empty($data1['results'][0]['address_components'][4]['long_name'])?$data1['results'][0]['address_components'][4]['long_name'].' ':' N/A'." , ";
    $address.=!empty($data1['results'][0]['address_components'][6]['long_name'])?$data1['results'][0]['address_components'][6]['long_name'].' ':' N/A';
    
	return $address;
    }else{
        $address="";
        return $address;
    }
}

## ..................code to get address according to latitude , longitude ends here ........................##


}
