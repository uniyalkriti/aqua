<?php
require_once('../admin/functions/common_function.php');
require_once('../admin/include/conectdb.php');
require_once('../admin/include/config.inc.php');
require_once('../admin/include/my-functions.php');
require_once('../client/include/classes/primary_sale.php');

$dealer_id = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['dealer_id'])));
$fromdate = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['from_date'])));
$todate = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['to_date'])));
global $dbc;
//$out = array();
//$dealer_sale = array();
//$date = date("Y-m-d");

$data = array();
$q = "SELECT order_id,created_person_id,csa_name, DATE_FORMAT(receive_date,'%d/%m/%Y') AS receive_date FROM user_primary_sales_order left join csa c on user_primary_sales_order.csa_id=c.c_id WHERE dealer_id = '$dealer_id' AND DATE_FORMAT(`sale_date`,'%Y-%m-%d') >= '$fromdate' AND DATE_FORMAT(`sale_date`,'%Y-%m-%d') <= '$todate' AND action = 1 ORDER BY user_primary_sales_order.created_date DESC";
//h1($q);
$rs = mysqli_query($dbc,$q);
 while ($row = mysqli_fetch_assoc($rs)) {
            //$id = $row['user_id'].$row['usolid'].$row['did'];
                //$out[$id] = $row;
            $out['orderid'] = $row['order_id'];
            $out['receive_date'] = $row['receive_date'];
            $out['csa_name'] = $row['csa_name'];
            $user = $row['created_person_id'];
                    $qn = mysqli_query($dbc,"SELECT CONCAT_WS(' ',first_name,middle_name, last_name) AS name FROM
                        person WHERE id=$user");
                    $qf = mysqli_fetch_assoc($qn);
                    $out['user_name'] = $qf['name'];
	$data[] = $out;
        }

$final_array = array("result" => $data);
$data = json_encode($final_array);
echo $data;



?>
