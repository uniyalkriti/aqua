<?php
//$dbc = @mysqli_connect('localhost','root','','msell-dsgroup-dms') OR die ('could not connect:' .mysqli_connect_error());
require_once ('../../../include/conectdb.php');
$id = $_GET['q'];
$product = $_GET['product'];
$csa = $_GET['csa'];
$q = "SELECT dealer_rate from ss_margin_per INNER JOIN ss_margin_wise_rate
            ON ss_margin_wise_rate.margin_id = ss_margin_per.margin_id WHERE ss_margin_per.ss_id=$csa AND product_id = $product AND mrp = $id order by ss_margin_wise_rate.id desc";
   
$result = mysqli_query($dbc,$q);
$row = mysqli_fetch_assoc($result);
echo $row['dealer_rate'];
?>