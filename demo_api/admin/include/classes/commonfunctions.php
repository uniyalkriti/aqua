<?php


function arraytolist($array,$joinwith){
	if($joinwith=='coma'){
		$list=join(',',$array);
	}elseif($joinwith=='and'){
		$list=join(' AND ',$array);
	}
return $list;
}


function check_duplicate($tablename,$data){
	$sql=mysql_query("select id from ".$tablename." where ".arraytolist($data,"and"));
	$rows=mysql_num_rows($sql);
	if($rows>0){
		return false;
	}else{
		return true;	
	}
}

function insert_update($tablename,$arraydata,$task,$condition,$code){
	if($task=='insert'){
		if(check_duplicate($tablename,$arraydata)){
			if(!($code=="")){ $arraydata[]=$code; }
			 $qry="insert into ".$tablename." set ".arraytolist($arraydata,"coma"); ;
				if(mysql_query($qry)){ return 1; }
		}else{ return 3; }
	}elseif($task=='update'){
		if(check_duplicate($tablename,$arraydata)){
			$qry="update ".$tablename." set ".arraytolist($arraydata,"coma")." where ".arraytolist($condition,"and") ;
			if(mysql_query($qry)){ return 2; }
		}else{ return 3; }
	}
}

function master_code($tablename,$suffix){
	$sql=mysql_query("select max(id) as id from ".$tablename);
	$rows=mysql_num_rows($sql);
	if($rows>0){
		$res=mysql_fetch_assoc($sql);
		$code=$res['id']+1;
	}else{
		$code=1;
	}
	$code=$suffix.$code;
return $code;
}




function deletedata($tablename,$id){
	$sql2=mysql_query("Delete from ".$tablename." where id='".$id."'");
	if(!$sql2)
	{
		die('Error in connection'.mysql_error());
		exit();
	}
}


function decimal($num){
	if(strstr($num,'.')){
		$x=explode('.',$num);
		if(strlen($x[1])>=2){
			return round($num,2);
		}else{
			return $num=$num.'0';			
		}
	}else{
		if($num!=''){
			return $num=$num.'.00';
		}else {
			return $num='0.00';
		}
		
	}
}


function convert_date($date,$format){
	$t=explode("-",$date);
	$date=$t[2]."-".$t[1]."-".$t[0];
	return $date;
}

function getlatlongbymccmnclaccid($mcc,$mnc,$lac,$cid){
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
}



// code to get address according to latitude , longitude
function getLocationByLatLng($lat,$lng){
//http://maps.googleapis.com/maps/api/geocode/json?latlng=28.4023003,77.3229817&sensor=true 28.531368627418,77.2377156544599
	$data = file_get_contents('http://maps.googleapis.com/maps/api/geocode/json?latlng='.$lat.','.$lng.'&sensor=true');
	$data1=json_decode($data, true);
	$address=$data1['results'][0]['address_components'][1]['long_name'].", ";
	$address.=$data1['results'][0]['address_components'][2]['long_name'].", ";
	$address.=$data1['results'][0]['address_components'][4]['long_name'].", ";
	$address.=$data1['results'][0]['address_components'][6]['long_name'];
	return $address;
}

// code to get driving distance according to latitude , longitude
function getDistanceByLatLng($lat1,$lng1,$lat2,$lng2){
//http://maps.googleapis.com/maps/api/distancematrix/json?origins=28.569628+77.253418&destinations=28.532233+77.261658&mode=driving&language=en-EN&sensor=true
	$data = file_get_contents('http://maps.googleapis.com/maps/api/distancematrix/json?origins='.$lat1.'+'.$lng1.'&destinations='.$lat2.'+'.$lng2.'&mode=driving&language=en-EN&sensor=true');
	$data1=json_decode($data, true);
	$distance=$data1['rows']['elements']['distance']['text'];
	return $distance;
}



function getDistanceBetweenPointsNew($latitude1, $longitude1, $latitude2, $longitude2, $unit = 'Mi') {
     $theta = $longitude1 - $longitude2;
     $distance = (sin(deg2rad($latitude1)) * sin(deg2rad($latitude2))) + (cos(deg2rad($latitude1)) * cos(deg2rad($latitude2)) * cos(deg2rad($theta)));
     $distance = acos($distance);
     $distance = rad2deg($distance);
     $distance = $distance * 60 * 1.1515; switch($unit) {
          case 'Mi': break; case 'Km' : $distance = $distance * 1.609344;
     }
     return (round($distance,2));
}


function recursive($code){
	$qry=mysql_query("select u_code,ucase(u_fullname) as u_fullname  from user_master where u_senior=trim('".$code."')");
	$num=mysql_num_rows($qry);
	if($num<=1){
		$res1=mysql_fetch_assoc($qry);
		if($res1['u_code']!=""){
			echo "<option value='".$res1['u_code']."'>".$res1['u_fullname']."</option>";
		}
	}
	else
	{
		while($res2=mysql_fetch_assoc($qry)){
			if($res2['u_code']!=""){
				echo "<option value='".$res2['u_code']."'>".$res2['u_fullname']."</option>";
				recursive($res2['u_code']);
			}
		}
	}
}

function recursiveall($code){
	$qry="";
	$res1="";
	$res2="";
	$qry=mysql_query("select u_code,ucase(u_fullname) as u_fullname  from user_master where u_senior=trim('".$code."')");
	$num=mysql_num_rows($qry);
	if($num<=1){
		$res1=mysql_fetch_assoc($qry);
		if($res1['u_code']!=""){
			echo "'".$res1['u_code']."',";
		}
	}
	else
	{
		while($res2=mysql_fetch_assoc($qry)){
			if($res2['u_code']!=""){
				echo "'".$res2['u_code']."',";
				recursiveall($res2['u_code']);
			}
		}
	}
}



function working_list($status) {
$listdata="<select name='working_status[]'>";
	if($status=='FieldWorking') {
		$listdata.="<option value='FieldWorking' selected='selected'>Field Working</option>";
	}else {
		$listdata.="<option value='FieldWorking' >Field Working</option>";		
	}
	if($status=='Meeting') {
		$listdata.="<option value='Meeting' selected='selected'>Meeting</option>";
	}else {
		$listdata.="<option value='Meeting'>Meeting</option>";		
	}
	if($status=='InTransit') {
		$listdata.="<option value='InTransit' selected='selected'>InTransit</option>";
	}else {
		$listdata.="<option value='InTransit'>InTransit</option>";
	}		
	if($status=='Sunday') {
		$listdata.="<option value='Sunday' selected='selected'>Sunday</option>";
	}else {
		$listdata.="<option value='Sunday'>Sunday</option>";
	}		
	if($status=='Leave') {
		$listdata.="<option value='Leave' selected='selected'>Leave</option>";
	}else {
		$listdata.="<option value='Leave'>Leave</option>";
	}
	if($status=='Weeklyoff') {
		$listdata.="<option value='Weeklyoff' selected='selected'>Weeklyoff</option>";
	}else {
		$listdata.="<option value='Weeklyoff'>Weeklyoff</option>";
	}
	if($status=='Holiday') {
		$listdata.="<option value='Holiday' selected='selected'>Holiday</option>";
	}else {
		$listdata.="<option value='Holiday'>Holiday</option>";
	}
$listdata.="</select>";
return $listdata;
	}

$data="";

function dealer_list($dealers,$d_code) {
	$data="<select name='dis_code[]' style='width:150px'>";
	foreach($dealers as $key => $value) {
		if($key==$d_code) {
			$data.="<option value='".$key."' selected='selected'>".strtoupper($value)."</option>";
		}else {
			$data.="<option value='".$key."'>".strtoupper($value)."</option>";		
		}
	}
	$data.="</select>"; 
	return $data;		
}

function location_list($locations,$l_code) {
	$data="<select name='l_code[]' style='width:150px'>";
	foreach($locations as $key => $value) {
		if($key==$l_code) {
			$data.="<option value='".$key."' selected='selected'>".strtoupper($value)."</option>";
		}else {
			$data.="<option value='".$key."'>".strtoupper($value)."</option>";		
		}
	}
	$data.="</select>";  
	return $data;		
}

function check_null($data){
	if($data!=''){
		return $data;
	}else{
		return "-";
	}
}

function propercase($str) {
		$str=ucwords(strtolower(check_null($str)));
		return $str;
}
?>
