<?php 
require_once('../../admin/include/conectdb.php');
 $dealerid=$_POST['id'];
global $dbc;

 $beatq = "SELECT location_id,location_7.name as location_name FROM `dealer_location_rate_list` INNER JOIN location_7 ON location_7.id = location_id WHERE  dealer_id='$dealerid' GROUP by location_id";
$runBeat = mysqli_query($dbc,$beatq);
$rowB = array();
$vart="<option value=''>Please Select</option>";
while($rowBeat = mysqli_fetch_assoc($runBeat))
{
  //$rowB[] = $rowBeat;
 $vart.="<option value='".$rowBeat['location_id']."'>".$rowBeat['location_name']."</option>";
}
echo $vart;



?>