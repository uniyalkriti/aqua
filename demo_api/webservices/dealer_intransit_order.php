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
$q = "SELECT order_id,created_person_id,csa_name,DATE_FORMAT(order_date,'%d/%m/%Y') AS sale_date FROM purchase_order left join csa c on purchase_order.csa_id=c.c_id WHERE DATE_FORMAT(`order_date`,'%Y-%m-%d') >= '$fromdate' AND DATE_FORMAT(`order_date`,'%Y-%m-%d') <= '$todate' AND dealer_id = '$dealer_id' AND challan_no = '' ORDER BY purchase_order.created_date DESC";
//h1($q);
$rs = mysqli_query($dbc,$q);
 while ($row = mysqli_fetch_assoc($rs)) {
            //$id = $row['user_id'].$row['usolid'].$row['did'];
                //$out[$id] = $row;
            $out['orderid'] = $row['order_id'];
            $out['order_date'] = $row['sale_date'];
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
