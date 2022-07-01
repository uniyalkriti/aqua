<?php
require_once('../admin/functions/common_function.php');
require_once('../admin/include/conectdb.php');
require_once('../admin/include/config.inc.php');
require_once('../admin/include/my-functions.php');
//require_once('../client/include/classes/primary_sale.php');

$dealer_id = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['dealer_id'])));
$fromdate = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['from_date'])));
$todate = mysqli_real_escape_string($dbc, trim(stripslashes($_GET['to_date'])));
global $dbc;
//$out = array();
//$dealer_sale = array();
//$date = date("Y-m-d");

$data = array();
// $q = "SELECT cod.id AS id,uso.date AS date,co.ch_date as ch_date,uso.user_id as user_id, uso.order_id AS orderid, uso.total_sale_value AS salevalue, SUM(cod.taxable_amt) AS tax, co.ch_dealer_id AS did FROM user_sales_order AS uso INNER JOIN challan_order_details AS cod ON uso.order_id=cod.order_id INNER JOIN challan_order AS co ON cod.ch_id=co.id WHERE uso.dealer_id = '$dealer_id' AND ch_date >='$fromdate' AND ch_date <='$todate'  GROUP BY uso.order_id";

// $rs = mysqli_query($dbc,$q);
//  while ($row = mysqli_fetch_assoc($rs)) {
//             //$id = $row['user_id'].$row['usolid'].$row['did'];
//                 //$out[$id] = $row;
//             $out['orderid'] = $row['orderid'];
//             $out['salevalue'] = $row['salevalue'];
//             $out['challan_value'] = $row['tax'];
//             $out['saledate'] = $row['date'];
//             $out['challandate'] = $row['ch_date'];
//            // $out['pid']= myrowval('catalog_product', 'name',$row['pid']);
//            // $out['did'] = myrowval('dealer', 'name', $row['did']);
//             //$out['cid']=  myrowval('complaint','complaint',$row['cid']);
//      $user = $row['user_id'];
//                     $qn = mysqli_query($dbc,"SELECT CONCAT_WS(' ',first_name,middle_name, last_name) AS name FROM
//                         person WHERE id=$user");
//                     $qf = mysqli_fetch_assoc($qn);
//                     $out['user_name'] = $qf['name'];
//  $data[] = $out;
//         }


$i = 0;
$j = 0;
$k = 0;
 $q = "SELECT challan_order.id,challan_order.discount_per,challan_order.ch_dealer_id,challan_order.ch_user_id,DATE_FORMAT(challan_order.date_added,'%d-%m-%Y') AS date_added,challan_order.ch_no,challan_order.discount_amt,challan_order.amount, DATE_FORMAT(ch_date, '%Y-%m-%d') AS ch_date,(SELECT SUM(taxable_amt) FROM challan_order_details WHERE ch_id=challan_order.id) as tamount FROM challan_order WHERE DATE_FORMAT(`ch_date`,'%Y%m%d')>=20180401 AND ch_dealer_id='19'";
        list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = '');
        if (!$opt)
            return $out;
        while ($row = mysqli_fetch_assoc($rs)) {
            $where = 'id = '.$row['ch_retailer_id'];
            $retailer_map =  myrowval('retailer', 'name',$where);
            $id1 = $row['id'];
            $id = $i;
            $out['result'][$id] = $row; // storing the item id

           $q1 = "SELECT challan_order_details.*, "
                    . "catalog_product.name,"
                    . "catalog_product.hsn_code FROM "
                    . "challan_order_details INNER JOIN catalog_product ON catalog_product.id = challan_order_details.product_id "
                    . " WHERE  ch_id = '$id1' GROUP BY challan_order_details.id";
            $rs1=mysqli_query($dbc,$q1);  
             while ($row1 = mysqli_fetch_assoc($rs1)) {
                $out['result'][$id]['challan_item'][$j]=$row1;
                $j++;

            }

             $q2 = "SELECT sum(vat_amt) AS tax FROM "
                    . "challan_order_details "
                    . " WHERE  ch_id = '$id1' GROUP BY challan_order_details.id";
            $rs2=mysqli_query($dbc,$q2);  
             $row2 = mysqli_fetch_assoc($rs2);
                $cgst=$row2['tax']/2;
                $sgst=$row2['tax']/2;

              $out['result'][$id]['LedgerEntry']['0'][LedgerName] ='CGST';
              $out['result'][$id]['LedgerEntry']['0'][Amount] ="$cgst";
               $out['result'][$id]['LedgerEntry']['1'][LedgerName] ='SGST';
              $out['result'][$id]['LedgerEntry']['1'][Amount] ="$sgst";
               $out['result'][$id]['LedgerEntry']['2'][LedgerName] ='Discount';
              $out['result'][$id]['LedgerEntry']['2'][Amount] ="$row[discount_amt]";

                  
            
           // $out[$id]['challan_item'] = $this->get_my_reference_array_direct($q, 'id');

            $data = $out;
                       $i++;
        }

header('Content-Type: application/json');
$data = json_encode($data);
echo $data;



?>
