<?php
error_reporting(0);
ini_set('max_execution_time','-1');
require_once('../admin/include/conectdb.php');
// $date = date('Y-m-d');
// $predate = date('Y-m-d',strtotime($date.' -1 day'));
// $predate='2019-05-01';
// $date='2019-05-13';
$fom_date=$_GET['from_date'];
$to_date=$_GET['to_date'];
$user_id=$_GET['user_id'];
if(!empty($user_id))
{
    $where=" AND user_id='$user_id'";
}

//$datet=date('Y-m-d',strtotime($date.' +1 day'));
echo $q = "select * from user_daily_attendance where DATE_FORMAT(work_date,'%Y-%m-%d') >= '$fom_date' AND DATE_FORMAT(work_date,'%Y-%m-%d') <= '$to_date' AND track_addrs=', , , ' $where";
$res = mysqli_query($dbc, $q);
$count = 0;
//$rowcount=mysqli_num_rows($res);
//echo $rowcount;die;
//echo $rowcount;die;
while ($row = mysqli_fetch_array($res)) 
{
	     $lat_lng1 = explode(',', $row['lat_lng']);
         $lat = $lat_lng1[0];
         $lng = $lat_lng1[1];
    if (empty($row['track_addrs']) || $row['track_addrs'] == ", , , " ) 
    {
    
            if ($row['mnc_mcc_lat_cellid'] != "0:0:0:0" || $row['mnc_mcc_lat_cellid'] != "false") 
            {
                $mmlc = explode(':', $row['mnc_mcc_lat_cellid']);
                $mnc = $mmlc[0];
                $mcc = $mmlc[1];
                $lac = $mmlc[2];
                $cellid = $mmlc[3];
                $latlng = getlatlongbymccmnclaccid($mnc, $mcc, $lac, $cellid);
                $lat_lng = explode(",", $latlng);
                $lat = $lat_lng[0];
                $lng = $lat_lng[1];
                $final = $lat . ',' . $lng;
                $address = getLocationByLatLng($lat, $lng);
                echo $q = "update user_daily_attendance set track_addrs = '$address',lat_lng = '$final' where id = '$row[id]' ";
                echo '<br/>';
                $res2 = mysqli_query($dbc, $q);
                $count++;
            }
       else {
           $address = getLocationByLatLng($lat, $lng);
            echo $q = "update user_daily_attendance set track_addrs = '$address' where id = '$row[id]' ";
            echo '<br/>';
            $res2 = mysqli_query($dbc, $q);
            $count++;
        }
    }
}

echo $count . " RECORDS UPDATED";


// code to get address according to latitude , longitude
function getLocationByLatLng($lat,$lng)
{
    if(($lat!='0') && ($lng!='0')){
//http://maps.googleapis.com/maps/api/geocode/json?latlng=28.4023003,77.3229817&sensor=true 28.531368627418,77.2377156544599
    $data = file_get_contents('https://maps.googleapis.com/maps/api/geocode/json?latlng='.$lat.','.$lng.'&key=AIzaSyCLLdtysWdVNuZ8yYzCSGzzPWo_77kHVeo&sensor=true');
    $data1=json_decode($data, true);
    $address=$data1['results'][0]['address_components'][1]['long_name'].", ";
    $address.=$data1['results'][0]['address_components'][2]['long_name'].", ";
    $address.=$data1['results'][0]['address_components'][4]['long_name'].", ";
    $address.=$data1['results'][0]['address_components'][6]['long_name'];
    return $address;
    }else{
        $address="";
        return $address;
    }
}

// code to get address according to mccmnclaccid

function getlatlongbymccmnclaccid($mcc,$mnc,$lac,$cid)
{
    
    if(($mcc!='0') && ($mnc!='0'))
    {
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
        if (strlen($str) > 10) 
        {
          $lat_tmp = unpack("l",$str[10].$str[9].$str[8].$str[7]);
          $lon_tmp = unpack("l",$str[14].$str[13].$str[12].$str[11]);
          $lon = $lon_tmp[1]/1000000;
          $lat = $lat_tmp[1]/1000000;
          $ll=$lat.",".$lon;
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


