<?php
require_once('../admin/functions/common_function.php');
require_once('../admin/include/conectdb.php');
require_once('../admin/include/config.inc.php');
require_once('../admin/include/my-functions.php');
// Here we get scheme details
if(isset($_GET['imei'])) $imei = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['imei']))); else $imei = 0;
if(isset($_GET['dealer_id'])) $dealer_id = mysqli_real_escape_string($dbc, trim(stripslashes(dealer_id))); else $dealer_id = 0;
$user_qry=mysqli_query($dbc,"select id from person where imei_number='".$imei."'") or die(mysqli_error($dbc));
$row=  mysqli_fetch_array($user_qry);
echo $person_id = $row[id];
//echo "select id from person where imei_number='".$imei."'";
if(!empty($imei))
{
    $q = "SELECT * FROM scheme INNER JOIN scheme_product_details USING(scheme_id) INNER JOIN scheme_dealer USING(scheme_id) INNER JOIN scheme_dealer_details USING(sd_id) INNER JOIN dealer_person USING(dealer_id) WHERE dealer_id = '$dealer_id' AND person_id = '$person_id'";
    echo $q;
}
?>