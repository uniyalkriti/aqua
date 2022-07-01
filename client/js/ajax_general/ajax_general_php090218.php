<?php
//phpinfo();
@session_start();
ob_start();
require_once('../../include/config.inc.php');
require_once(BASE_URI_ROOT . ADMINFOLDER . MSYM . 'include' . MSYM . 'my-functions.php');
// This function will prepare the ajax response text which will be send to ajax call ends here
// echo'FALSE<$>Sorry no record found';
if (isset($_SESSION[SESS . 'user'])) {
    //if at some instance we are making a post request
    if (isset($_POST['wcase'])) {
        $_GET['pid'] = $_POST['pid'];
        $_GET['wcase'] = $_POST['wcase'];
}
    if (isset($_GET['pid']) && !empty($_GET['pid'])) {
        $id = $_GET['pid'];
        $wcase = $_GET['wcase'];
        $state_id  = $_SESSION[SESS . 'data']['state_id'];
        $dealer_id = $_SESSION[SESS . 'data']['dealer_id'];

        switch ($wcase) {
            case'nestingqty': {
                    $ids = explode('|', $id);
                    $q = "SELECT qty FROM nesting_item WHERE nestingId = '{$ids[1]}' AND itemId = {$ids[0]} LIMIT 1";
                    $r = mysqli_query($dbc, $q);
                    if ($r) {
                        if (mysqli_num_rows($r) > 0) {
                            echo'TRUE<$>';
                            $row = mysqli_fetch_assoc($r);
                            //echo $row['price'];
                            //echo $row['etc'];
                            echo $row['qty'];
                        } else
                            echo'FALSE<$>Sorry no record found';
                    }
                    break;
                }
                
             case 'get_retailer_rate':
             {
                $product_id = $id;
                $state= $_SESSION[SESS . 'data']['state_id'];
                $qrr = 'SELECT retailer_rate as rate from product_rate_list where product_id = '.$product_id.' AND state_id='.$state.'';
                $rrr = mysqli_query($dbc, $qrr);
                if ($rrr) {
                    if (mysqli_num_rows($rrr) > 0) {
                        echo'TRUE<$>';
                        $row = mysqli_fetch_assoc($rrr);
                        echo $row['rate'];                           
                    } else{
                        echo'FALSE<$>Sorry no record found';
                    }
                }
                break;
            }

        
        /* Below case is to fetch retailer rate and available stock */
        case 'get_retailer_rate_edit':
        {
            $product_id = $id;
            $state= $_SESSION[SESS . 'data']['state_id'];

            $qrr = 'SELECT r.retailer_rate as rate, s.qty from product_rate_list r LEFT JOIN stock s ON r.product_id=s.product_id where r.product_id = '.$product_id.' AND r.state_id='.$state.'';
                   $rrr = mysqli_query($dbc, $qrr);
                    if ($rrr) {
                        if (mysqli_num_rows($rrr) > 0) {
                            echo'TRUE<$>';
                            $row = mysqli_fetch_assoc($rrr);
                          echo $row['rate'].'<$$>'.$row['qty'];                           
                        } else
                            echo'FALSE<$>Sorry no record found';
                    }
                    break;
        }


        case 'get_saleable_non_saleable':
        {
                    
                      $q = "SELECT `id`,`name` FROM `complaint_type` WHERE  `saleable_non_saleable`=$id";
                    echo 'TRUE<$>'.$q;
                     break;
        }
            case'get_total_sale_value': {

                    $q = "SELECT SUM(product_rate) AS rate FROM challan_order_details WHERE challan_no = '$id'  LIMIT 1";
                    $r = mysqli_query($dbc, $q);
                    if ($r) {
                        if (mysqli_num_rows($r) > 0) {
                            echo'TRUE<$>';
                            $row = mysqli_fetch_assoc($r);
                            //echo $row['price'];
                            //echo $row['etc'];
                            echo $row['rate'];
                        } else
                            echo'FALSE<$>Sorry no record found';
                    }
                    break;
                }
            case'get_product_qty': {
                    $id = explode('#', $id);
                    $q = "SELECT SUM(quantity) AS quantity,user_id FROM user_sales_order_details INNER JOIN user_sales_order ON user_sales_order.order_id = user_sales_order_details.order_id WHERE product_id = '$id[0]]' AND user_sales_order_details.order_id = '$id[1]'  LIMIT 1";
                    $r = mysqli_query($dbc, $q);
                    if ($r) {
                        if (mysqli_num_rows($r) > 0) {
                            echo'TRUE<$>';
                            $row = mysqli_fetch_assoc($r);
                            //echo $row['price'];
                            //echo $row['etc'];
                            echo $row['quantity'] . '<$$>' . $row['user_id'];
                        } else
                            echo'FALSE<$>Sorry no record found';
                    }
                    break;
                }
            case'get_product_tax': {
                    $id = explode('#', $id);
                    $q = "SELECT tax FROM catalog_product_rate_list INNER JOIN user_primary_sales_order_details  ON user_primary_sales_order_details.product_id = catalog_product_rate_list.catalog_product_id  WHERE user_primary_sales_order_details.id = '$id[0]'  LIMIT 1";

                    $r = mysqli_query($dbc, $q);
                    if ($r) {
                        if (mysqli_num_rows($r) > 0) {
                            echo'TRUE<$>';
                            $row = mysqli_fetch_assoc($r);
                            //echo $row['price'];
                            //echo $row['etc'];
                            echo $row['tax'] . '<$$>' . $row['user_id'];
                            // echo $q;
                        } else
                            echo'FALSE<$>Sorry no record found';
                    }
                    break;
                }
                case'get_calculate_rate': {
                    
                    $q = "SELECT rate FROM catalog_product_rate_list cprl  WHERE cprl.catalog_product_id = '$id' AND stateId='".$_SESSION[SESS.'data']['state_id']."'  LIMIT 1";

                    $r = mysqli_query($dbc, $q);
                    if ($r) {
                        if (mysqli_num_rows($r) > 0) {
                            echo'TRUE<$>';
                            $row = mysqli_fetch_assoc($r);
                            $rate = $row['rate'];
                       
                            echo $rate;
                            // echo $q;
                        } else
                            echo'FALSE<$>Sorry no record found';
                    }
                    break;
                }
                

            case'get_total_challan_value': {
                    $id = rtrim($id, ',');
                    $q = "SELECT SUM(product_rate*ch_qty) AS rate FROM challan_order_details WHERE challan_no IN ($id)  LIMIT 1";
                    $r = mysqli_query($dbc, $q);
                    if ($r) {
                        $qq = "SELECT SUM(pay_amount) AS pamt FROM challan_order_wise_payment INNER JOIN challan_order_wise_payment_details USING(pay_id) WHERE challan_no IN ($id)";
                        $rr = mysqli_query($dbc, $qq);
                        if (mysqli_num_rows($r) > 0) {

                            echo'TRUE<$>';
                            $row = mysqli_fetch_assoc($r);
                            $rows = mysqli_fetch_assoc($rr);
                            $rate = $row['rate'] - $rows['pamt'];
                            echo $rate;
                        } else
                            echo'FALSE<$>Sorry no record found';
                    }
                    break;
                }
            case'get_batch_rate': {
                    $ids = explode('<$>', $id);
                    $q = "SELECT rate, ostock,batch_no FROM catalog_product_details WHERE id = '{$ids[1]}' LIMIT 1";
                    //echo 'TRUE<$>'.$q;
                    $r = mysqli_query($dbc, $q);
                    if ($r) {
                        if (mysqli_num_rows($r) > 0) {
                            echo'TRUE<$>';
                            $row = mysqli_fetch_assoc($r);
                            //echo $row['price'];
                            //echo $row['etc'];
                            echo $row['rate'] . '<$$>' . $row['ostock'] . '<$$>' . $row['batch_no'];
                        } else
                            echo'FALSE<$>Sorry no record found';
                    }
                    break;
                }
            case'tax_value': // from the spa load enquiry
                {
                    $q = "SELECT * FROM tax WHERE tax = '$id' LIMIT 1";
                    $r = mysqli_query($dbc, $q);
                    if ($r) {
                        if (mysqli_num_rows($r) > 0) {
                            echo'TRUE<$>';
                            $row = mysqli_fetch_assoc($r);
                            //echo $row['price'];
                            //echo $row['etc'];
                            echo $row['taxvalue'];
                        } else
                            echo'FALSE<$>Sorry no record found';
                    }
                    break;
                }
                
            case'get-retailer-location': // from the spa load enquiry
                {
                    $q = "SELECT location_id FROM retailer WHERE id = '$id' LIMIT 1";
                    $r = mysqli_query($dbc, $q);
                    if ($r) {
                        if (mysqli_num_rows($r) > 0) {
                            echo'TRUE<$>';
                            $row = mysqli_fetch_assoc($r);
                            //echo $row['price'];
                            //echo $row['etc'];
                            echo $row['location_id'];
                        } else
                            echo'FALSE<$>Sorry no record found';
                    }
                    break;
                }
            case'get_unique_password': // from the spa load enquiry
                {
                    $length = 6;
                    $alphabets = range('A', 'Z');
                    $numbers = range('0', '9');
                    $final_array = array_merge($alphabets, $numbers);
                    while ($length >= 1) {
                        $key = array_rand($final_array);
                        $password .= $final_array[$key];
                        $length--;
                    }

                    if (strlen($password) > 0) {
                        echo'TRUE<$>';

                        echo $password;
                    } else
                        echo'FALSE<$>Sorry no record found';
                    break;
                }
            //get_unique_password
            case'sf_stock': // from the spa load enquiry
                {
                    $q = "SELECT SUM(balance) as balance FROM item_partial_return_qty WHERE itemId = '$id' LIMIT 1";
                    $r = mysqli_query($dbc, $q);
                    if ($r) {
                        if (mysqli_num_rows($r) > 0) {
                            echo'TRUE<$>';
                            $row = mysqli_fetch_assoc($r);
                            //echo $row['price'];
                            //echo $row['etc'];
                            echo $row['balance'];
                        } else
                            echo'FALSE<$>Sorry no record found';
                    }
                    break;
                }
            case'ch_annexure': // from the spa load enquiry
                {
                    $q = "SELECT partyId, partyname, DATE_FORMAT(challan_date,'%d/%m/%Y') as cdata  FROM ch_annexure INNER JOIN party USING(partyId) WHERE chanum = '$id' LIMIT 1";
                    $r = mysqli_query($dbc, $q);
                    if ($r) {
                        if (mysqli_num_rows($r) > 0) {
                            echo'TRUE<$>';
                            $row = mysqli_fetch_assoc($r);
                            echo $row['cdata']; //'<$$>'.$row['partyId'].'<$$>'.$row['partyname'];
                        } else
                            echo'FALSE<$>Sorry no record found';
                    }
                    break;
                }
            case'rgp_item_detail': // from the spa load enquiry
                {
                    $id = explode('<$>', $id);
                    $itemId = $id[0];
                    $chrgpId = $id[1];
                    $q = "SELECT * FROM ch_rgp_item WHERE chrgpId = '$chrgpId' AND itemId = '$itemId' LIMIT 1";
                    $r = mysqli_query($dbc, $q);
                    if ($r) {
                        if (mysqli_num_rows($r) > 0) {
                            echo'TRUE<$>';
                            $row = mysqli_fetch_assoc($r);
                            echo $row['job_process'] . '<$$>' . $row['unit'] . '<$$>' . $row['qty'] . '<$$>' . $row['goodvalue'];
                        } else
                            echo'FALSE<$>Sorry no record found';
                    }
                    break;
                }
            case'get-product-rate': // from the spa load enquiry
                {
                    $q = "SELECT rate FROM catalog_product_rate_list cprl  WHERE cprl.catalog_product_id = '$id' AND stateId='".$_SESSION[SESS.'data']['state_id']."'  LIMIT 1";
                    echo 'TRUE<$>'.$q;
                    $r = mysqli_query($dbc, $q);
                    if ($r) {
                        if (mysqli_num_rows($r) > 0) {
                            echo'TRUE<$>';
                            $row = mysqli_fetch_assoc($r);
                            //echo $row['product_id'].'<$$>'.$row['rate'].'<$$>'.$row['batch_no'];
                            echo $row['rate'];
                        } else
                            echo'FALSE<$>Sorry no record found';
                    }
                    break;
                }
                case'get-mrp-product': // from the spa load enquiry
                {
                    $q = "SELECT dealer_rate as rate FROM `product_rate_list` cprl INNER JOIN state ON state.stateid = cprl.state_id  INNER JOIN person ON person.state_id = state.stateid INNER JOIN user_dealer_retailer udr ON udr.user_id = person.id WHERE cprl.product_id = '$id' AND dealer_id ='".$_SESSION[SESS.'data']['dealer_id']."'  LIMIT 1";
                    //echo 'TRUE<$>'.$q;
                    $r = mysqli_query($dbc, $q);
                    if ($r) {
                        if (mysqli_num_rows($r) > 0) {
                            echo'TRUE<$>';
                            $row = mysqli_fetch_assoc($r);
                            //echo $row['product_id'].'<$$>'.$row['rate'].'<$$>'.$row['batch_no'];
                            echo $row['rate'] ;
                        } else
                            echo'FALSE<$>Sorry no record found';
                    }
                    break;
                }
            case'get_product_details': // from the spa load enquiry
                {
                    $id = explode('<$>', $id);
                    $product_id = $id[0];
                    $dealer_id = $id[1];
                    $dealer_id = $_SESSION[SESS . 'data']['dealer_id'];                   
                    $multiple_rate_list_status = $_SESSION[SESS . 'constant']['multiple_rate_list_status'];
                    if ($multiple_rate_list_status == '0')
                        $q = "SELECT rate AS price FROM catalog_product WHERE id = '$product_id'   LIMIT 1";
                    else
                        $q = "SELECT pr_rate AS price FROM user_primary_sales_order upso  INNER JOIN user_primary_sales_order_details upsod ON upso.order_id = upsod.order_id  WHERE product_id = '$product_id' AND dealer_id = $dealer_id ";
                    // echo 'TRUE<$>'.$q;
                    $r = mysqli_query($dbc, $q);
                    if ($r) {
                        if (mysqli_num_rows($r) > 0) {
                            echo'TRUE<$>';
                            $row = mysqli_fetch_assoc($r);
                           echo $row['price'];
                        } else
                            echo'FALSE<$>Sorry no record found';
                    }
                    break;
                }
                 case'get_product_mrp_refund': // from the spa load enquiry
                { 
                    $id = explode('<$>', $id);
                    $product_id = $id[0];
                    $dealer_id = $id[1];
                    $state= $_SESSION[SESS . 'data']['state_id'];

                     /* PUNEET */

                      $q = "SELECT r.mrp as rate,r.retailer_rate FROM product_rate_list r WHERE r.product_id = '$product_id' AND r.state_id = '$state'";

                     // echo 'TRUE<$>'.$q;

                    $r = mysqli_query($dbc, $q);
                    if ($r) {
                        if (mysqli_num_rows($r) > 0) {
                            echo'TRUE<$>';
                            $row = mysqli_fetch_assoc($r);
                            echo $row['rate'].'<$$>'.$row['retailer_rate'];
                        } else
                            echo'FALSE<$>Sorry no record found';
                    }
                    break;
                }
                 case'get_product_mrp': // from the spa load enquiry
                { 
                    $id = explode('<$>', $id);
                    $product_id = $id[0];
                    $dealer_id = $id[1];
                    $state= $_SESSION[SESS . 'data']['state_id'];

                     /* PUNEET */

                      $q = "SELECT s.product_id,r.mrp as rate,s.qty, r.retailer_rate FROM product_rate_list r JOIN stock s ON r.product_id=s.product_id WHERE r.product_id = '$product_id' AND r.state_id = '$state' AND s.dealer_id='$dealer_id'";

                     // echo 'TRUE<$>'.$q;

                    $r = mysqli_query($dbc, $q);
                    if ($r) {
                        if (mysqli_num_rows($r) > 0) {
                            echo'TRUE<$>';
                            $row = mysqli_fetch_assoc($r);
                            echo $row['rate'].'<$$>'.$row['qty'].'<$$>'.$row['retailer_rate'];
                        } else
                            echo'FALSE<$>Sorry no record found';
                    }
                    break;
                }
                case'get_product_mrp_replace': // from the spa load enquiry
                { 
                    $id = explode('<$>', $id);
                    $product_id = $id[0];
                    $dealer_id = $id[1];
                    $state= $_SESSION[SESS . 'data']['state_id'];
                   // $dealer_id = $_SESSION[SESS . 'data']['dealer_id'];                   
                   // $multiple_rate_list_status = $_SESSION[SESS . 'constant']['multiple_rate_list_status'];
                  
                      //$q = "SELECT pr_rate AS price FROM user_primary_sales_order upso  INNER JOIN user_primary_sales_order_details upsod ON upso.order_id = upsod.order_id  WHERE product_id = '$product_id' AND dealer_id = $dealer_id AND expiry_date >CURDATE()  ";
                      $q = "SELECT rate FROM catalog_product_rate_list WHERE catalog_product_id = '$product_id' AND stateId = '$state' ";
                     //echo 'TRUE<$>'.$q;
                    $r = mysqli_query($dbc, $q);
                    if ($r) {
                        if (mysqli_num_rows($r) > 0) {
                            echo'TRUE<$>';
                            $row = mysqli_fetch_assoc($r);
                            echo $row['rate'];
                        } else
                            echo'FALSE<$>Sorry no record found';
                    }
                    break;
                }
                
                case'get_comunity_code': // from the spa load enquiry
                {
                   $state= $_SESSION[SESS . 'data']['state_id'];
                    $id = explode('<$>', $id);
                    $product_id = $id[0];
                    $dealer_id = $id[1];
                    $dealer_id = $_SESSION[SESS . 'data']['dealer_id'];                   
                             
                        $q = "SELECT comunity_code FROM catalog_product_rate_list WHERE catalog_product_id = '$product_id' AND stateId='$state' ";
                     //  echo 'true<$>'.$q;
                    $r = mysqli_query($dbc, $q);
                    if ($r) {
                        if (mysqli_num_rows($r) > 0) {
                            echo'TRUE<$>';
                            $row = mysqli_fetch_assoc($r);
                            //if($state==28){
                                // echo 0.0;
                            //}else{
                                echo $row['comunity_code'];
                            //}
                            
                        } else
                            echo'FALSE<$>Sorry no record found';
                    }
                    break;
                }
                case'get_product_vat_rate': {
                
                   echo'TRUE<$>5';
                    break;
                                                           
                }
                case'get_mrp_vat': // from the spa load enquiry
                { 
                    
                    $id = explode('<$>', $id);
                    $product_id = $id[0];
                    $dealer_id = $id[1];
                    $dealer_id = $_SESSION[SESS . 'data']['dealer_id'];                   
                   // $multiple_rate_list_status = $_SESSION[SESS . 'constant']['multiple_rate_list_status'];
                  
                     echo $q = "SELECT rate,tax FROM catalog_product_rate_list INNER JOIN state ON state.stateid = catalog_product_rate_list.stateId INNER JOIN person ON person.state_id = state.stateid INNER JOIN user_dealer_retailer udr ON udr.user_id = person.id  WHERE catalog_product_id = '$product_id' AND dealer_id = $dealer_id ";                   
                     //echo 'TRUE<$>'.$q; break;
                    $r = mysqli_query($dbc, $q);
                    if ($r) {
                        if (mysqli_num_rows($r) > 0) {
                            echo'TRUE<$>';
                            $row = mysqli_fetch_assoc($r);
                          echo $row['rate'].'<$$>'.$row['tax'];                           
                        } else
                            echo'FALSE<$>Sorry no record found';
                    }
                    break;
                }
                

               /*                 case 'get_retailer_rate':
                        {
                            $state_id = $_SESSION[SESS . 'data']['state_id'];
                                        $product_id = $id;

                            $q = "SELECT  retailer_rate FROM product_rate_list where product_id= '.$product_id.' AND state_id='$state_id'";
                                   $r = mysqli_query($dbc, $q);
                                    if ($r) {
                                        if (mysqli_num_rows($r) > 0) {
                                            echo'TRUE<$>';
                                            $row = mysqli_fetch_assoc($r);
                                          echo $row['retailer_rate'];                           
                                        } else
                                            echo'FALSE<$>Sorry no record found';
                                    }
                                    break;
                        }
*/


                case 'get_product_gst':
                {
                    $product_id = $id;
                    $q = 'SELECT igst as vat from _gst INNER JOIN catalog_product cp ON cp.hsn_code = _gst.hsn_code where cp.id = '.$product_id.'';
                    
                    $r = mysqli_query($dbc, $q);
                    if ($r) {
                        if (mysqli_num_rows($r) > 0) {
                            echo'TRUE<$>';
                            $row = mysqli_fetch_assoc($r);
                            echo number_format($row['vat'],2);
                        } else
                        echo'FALSE<$>Sorry no record found';
                    }
                    break;
                }
                
//                    case 'get_product_hsn':
//		{
//                        $product_id = $id;
//			$q = 'SELECT hsn_code from catalog_product  where id = '.$product_id.'';
//                   $r = mysqli_query($dbc, $q);
//                    if ($r) {
//                        if (mysqli_num_rows($r) > 0) {
//                            echo'TRUE<$>';
//                            $row = mysqli_fetch_assoc($r);
//                          echo $row['hsn_code'];                           
//                        } else
//                            echo'FALSE<$>Sorry no record found';
//                    }
//                    break;
//		}
                
                
                
            //
            case'get_stock': // from the spa load enquiry
                {
                    /*$ids = explode('|',$id);
                    $sesId = $_SESSION[SESS . 'csess'];
                    $q1 = "SELECT SUM(qty) AS issue FROM challan_order INNER JOIN `challan_order_details` ON challan_order.id = challan_order_details.ch_id WHERE challan_order_details.product_id = '$ids[1]' AND  challan_order_details.mrp='$ids[0]'  AND ch_dealer_id = '".$_SESSION[SESS.'data']['dealer_id']."' ";
                    
                    $r1 = mysqli_query($dbc, $q1);
                    $d1 = mysqli_fetch_assoc($r1);
                    if ($d1['issue'] == '') {
                        $d1['issue'] = 0;
                    }
                    $q2 = "SELECT SUM(quantity)as quantity FROM user_primary_sales_order_details  usod INNER JOIN user_primary_sales_order uso ON uso.order_id = usod.order_id WHERE usod.product_id = '$ids[1]' AND usod.rate='$ids[0]' AND uso.dealer_id = '".$_SESSION[SESS.'data']['dealer_id']."'  ";
                  
                    $r2 = mysqli_query($dbc, $q2);
                    $d2 = mysqli_fetch_assoc($r2);
                    if ($d2['quantity'] == '') {
                        $d2['quantity'] = 0;
                    }


                    $q3 ="SELECT IF(ISNULL(sum(`damage_order_details`.`qty`)),0,sum(`damage_order_details`.`qty`)) AS `saleable_qty` from (`damage_order` join `damage_order_details` on((`damage_order_details`.`ch_id` = `damage_order`.`id`))) where ((`damage_order_details`.`product_id` = '$ids[1]') and (`damage_order`.`ch_dealer_id` = '".$_SESSION[SESS.'data']['dealer_id']."') and (`damage_order`.`saleable_non_saleable` = 1))";
                    
                    $r3 = mysqli_query($dbc, $q3);
                    $d3 = mysqli_fetch_assoc($r3);
                    if ($d3['saleable_qty'] == '') {
                        $d3['saleable_qty'] = 0;
                    }

                   $q4 ="SELECT SUM(qty) as qty from `stock_manual` where `product_id` = '$ids[1]' AND `dealer_id` = '".$_SESSION[SESS.'data']['dealer_id']."'";
                    
                    $r4 = mysqli_query($dbc, $q4);
                    $d4 = mysqli_fetch_assoc($r4);
                    if ($d4['qty'] == '') {
                        $d4['qty'] = 0;
                    }

                    //here we get stock
                    $stock =($d4['qty'])+($d2['quantity']+$d3['saleable_qty']) - $d1['issue'];
                    echo'TRUE<$>' . $stock;*/

                    /*<puneet>*/
                    
                    $ids = explode('|',$id);
                    $product_id = $ids[1];
                    
                    $get_stock_q = "SELECT qty FROM `stock` WHERE `product_id` = $product_id AND `dealer_id` = ".$_SESSION[SESS.'data']['dealer_id']."";
                    $get_stock_e = mysqli_query($dbc, $get_stock_q);
                    $stock = mysqli_fetch_assoc($get_stock_e);
                    echo'TRUE<$>' . $stock['qty'];

                    /*</puneet>*/

                    break;
                }
                
                
             

                
            //get_stock	
            case'get_rate': // from the spa load enquiry
                {
                    $q = "SELECT * FROM item WHERE itemId = '$id' LIMIT 1";
                    $r = mysqli_query($dbc, $q);
                    if ($r) {
                        if (mysqli_num_rows($r) > 0) {
                            echo'TRUE<$>';
                            $row = mysqli_fetch_assoc($r);
                            //echo $row['price'];
                            //echo $row['etc'];
                            echo $row['price'];
                        } else
                            echo'FALSE<$>Sorry no record found';
                    }
                    break;
                }
            case'get_lineno': // from the spa load enquiry
                {

                    $id = explode('<$>', $id);

                    $itemId = $id[0];
                    $wpoId = $id[1];
                    $q = "SELECT lineno,rate FROM work_po_item  WHERE itemId = '$itemId' AND wpoId = '$wpoId' LIMIT 1";
                    $r = mysqli_query($dbc, $q);
                    if ($r) {
                        if (mysqli_num_rows($r) > 0) {
                            echo'TRUE<$>';
                            $row = mysqli_fetch_assoc($r);
                            //echo $row['price'];
                            //echo $row['etc'];
                            echo $row['lineno'] . '<$$>' . $row['rate'];
                        } else
                            echo'FALSE<$>Sorry no record found';
                    }
                    break;
                }
            //get_lineno	
            case'stock_issue': // from the spa load enquiry
                {

                    $qty = get_central_stock($id);
                    echo'TRUE<$>';
                    echo $qty;
                    break;
                }
            case'stock_return': // from the spa load enquiry
                {
                    $q = "SELECT SUM(qty) as qty FROM stock_issue_item WHERE itemId = '$id'";
                    $r = mysqli_query($dbc, $q);
                    if ($r) {
                        if (mysqli_num_rows($r) > 0) {
                            echo'TRUE<$>';
                            $row = mysqli_fetch_assoc($r);
                            echo $row['qty'];
                        } else
                            echo'FALSE<$>Sorry no record found';
                    }
                    break;
                }
            case'gate_qty': // from the spa load enquiry
                {
                    $data = explode('-', $id);
                    $itemId = $data['0'];
                    $poId = $data['1'];
                    $q = "SELECT * FROM po_item WHERE itemId = '$itemId' AND poId = $poId LIMIT 1";
                    $r = mysqli_query($dbc, $q);
                    if ($r) {
                        if (mysqli_num_rows($r) > 0) {
                            echo'TRUE<$>';
                            $row = mysqli_fetch_assoc($r);
                            //echo $row['price'];
                            //echo $row['etc'];
                            echo $row['qty'];
                        } else
                            echo'FALSE<$>Sorry no record found';
                    }
                    break;
                }
            case'get_rate_qty': // from the spa load enquiry
                {
                    $q = "SELECT * FROM pr_item INNER JOIN item USING(itemId) WHERE itemId = '$id' LIMIT 1";
                    $r = mysqli_query($dbc, $q);
                    if ($r) {
                        if (mysqli_num_rows($r) > 0) {
                            echo'TRUE<$>';
                            $row = mysqli_fetch_assoc($r);
                            //echo $row['price'];
                            //echo $row['etc'];
                            echo $row['price'] . '<$$>' . $row['qty'];
                        } else
                            echo'FALSE<$>Sorry no record found';
                    }
                    break;
                }
            case'get_ch_item': // from the spa load enquiry
                {
                    $q = "SELECT * FROM ch_annexure_item INNER JOIN item USING(itemId) WHERE itemId = '$id' LIMIT 1";
                    $r = mysqli_query($dbc, $q);
                    if ($r) {
                        if (mysqli_num_rows($r) > 0) {
                            echo'TRUE<$>';
                            $row = mysqli_fetch_assoc($r);
                            //echo $row['price'];
                            //echo $row['etc'];	
                            echo $row['job_process'] . '<$$>' . $row['qty'] . '<$$>' . $row['unit'] . '<$$>' . $row['goodvalue'];
                        } else
                            echo'FALSE<$>Sorry no record found';
                    }
                    break;
                }
            case'getrecdata': // from the spa load enquiry
                {
                    $q = "SELECT DATE_FORMAT(challan_date,'%e/%d/%Y') AS challan_date,partyId,partyname,chanum FROM ch_rgp   INNER JOIN party USING(partyId) WHERE chrgpId = '$id' LIMIT 1";
                    $r = mysqli_query($dbc, $q);
                    if ($r) {
                        if (mysqli_num_rows($r) > 0) {
                            echo'TRUE<$>';
                            $row = mysqli_fetch_assoc($r);
                            //echo $row['price'];
                            //echo $row['etc'];
                            echo $row['challan_date'] . '<$$>' . $row['chanum'];
                        } else
                            echo'FALSE<$>Sorry no record found';
                    }
                    break;
                }
                
                ################################ getPendingBillNo ENDS ################################

                /* PUNEET */

            case 'getItemDetails':
                {
                    $r_id = $_POST['r_id'];
                    $q1 = "SELECT DISTINCT l1_id as r_state FROM retailer INNER JOIN location_view ON l5_id=location_id WHERE retailer.id=$r_id LIMIT 1";
                    // h1($q1);

                    $q1_e = mysqli_query($dbc, $q1);
                    $rt = mysqli_fetch_assoc($q1_e);

                    $st = 0;
                    if($rt['r_state']==$state_id)
                    {
                        $st = 1;    
                    }

                    $q = "SELECT CONCAT_WS('/',CAST(s.mrp as decimal(10,2)),s.qty,s.batch_no,DATE_FORMAT(s.mfg,'%b-%Y')) as mrp_dis,CAST(s.mrp as decimal(10,2)) as mrp,(CASE WHEN(t.igst IS NULL) THEN 0 ELSE t.igst END) as gst, (CASE WHEN(t.sgst IS NULL) THEN 0 ELSE t.sgst END) as sgst, (CASE WHEN(t.cgst IS NULL) THEN 0 ELSE t.cgst END) as cgst
                        FROM catalog_product p 
                        INNER JOIN stock s ON p.id=s.product_id 
                        LEFT JOIN _gst t ON p.hsn_code=t.hsn_code 
                        WHERE s.dealer_id='$dealer_id' AND p.id =  '$id'";

                    // h1($q);

                    $item_data_e = mysqli_query($dbc, $q);

                    if($item_data_e)
                    {
                        $item_data = array();
                        while($row = mysqli_fetch_array($item_data_e))
                        {
                            $mrp = $row['mrp'];
                            $item_data['mrp'][$mrp] = $row['mrp_dis'];
                            $item_data['gst']   = $row['gst'];
                            $item_data['sgst']  = $row['sgst'];
                            $item_data['cgst']  = $row['cgst'];
                            $item_data['st']    = $st;
                        }
                        
                        echo json_encode(array('exception'=>FALSE,'data'=>$item_data));
                    }else{
                        echo json_encode(array('exception'=>TRUE,'data'=>'No Records.'));
                    }
                }
                break;

            case 'rateNstock':
                {
                    global $dbc;
                	$mrp = $_POST['mrp'];
                    
                    $qry = "SELECT t.igst,s.qty,s.rate,s.product_id FROM stock s
                    INNER JOIN catalog_product p ON s.product_id=p.id 
                    LEFT JOIN _gst t ON p.hsn_code=t.hsn_code WHERE s.mrp='$mrp' AND s.product_id=$id AND s.dealer_id=$dealer_id";
                    
                    $qry_e = mysqli_query($dbc,$qry);
                    $row = mysqli_fetch_assoc($qry_e);
                    
                    echo json_encode(array('exception'=>FALSE,'data'=>$row));
                }
                break;



                /*Naveen Ji  */
            case 'getDealerRate':
                {
                    
        $location_id = (isset($_SESSION[SESS . 'data']['state_id']))? $_SESSION[SESS . 'data']['state_id']: 0;
        $product_id = (isset($_POST['pid'])? $_POST['pid'] : 0);
    /* echo json_encode(array('location_id'=>$location_id, 'product_id'=>$_POST['pid'])); die();*/

                    $q = "SELECT CAST(prl.mrp as decimal(10,2)) as mrp, (CASE WHEN(t.igst IS NULL) THEN 0 ELSE t.igst END) as gst 
                        FROM product_rate_list prl
                        INNER JOIN catalog_product p  ON p.id = prl.product_id 
                        LEFT JOIN _gst t ON  p.hsn_code=t.hsn_code 
                        WHERE prl.state_id='$location_id' AND prl.product_id =  '$product_id'";
//                     h1($q); die();
                    $item_data_e = mysqli_query($dbc, $q);
                    $drate = 0; $finaldrate = 0;
                    if($item_data_e)
                    {                        
                        $item_data = array();
                        while($row = mysqli_fetch_array($item_data_e))
                        {
                            /*if($rows['product_gst'])
                         {
                          $r_rate = $mrp-($mrp*25/100);
                           $d_rate = $r_rate-($r_rate*7.33/100);
                         }else{
                          $r_rate = $mrp-($mrp*18/100);
                          $d_rate = $r_rate-($r_rate*7/100);
                         }*/

                            if($row['gst'] > 0)
                            {  $drate = ( $row['mrp'] - ($row['mrp'] * .25) );  
                                $finaldrate = ( $drate - ($drate *.0733) );
                            }   
                            else{  
                                $drate = ( $row['mrp'] - ($row['mrp'] * .18) );  
                                $finaldrate = ( $drate - ($drate *.07) );
                            } 
                            $item_data['mrp'] = $row['mrp'];
                            $item_data['gst']    = $row['gst'];
                            $item_data['dealer_rate'] = round($finaldrate,2);
                        }
                        
                        echo json_encode(array('exception'=>FALSE,'data'=>$item_data));
                    }else{
                        echo json_encode(array('exception'=>TRUE,'data'=>'No Records.'));
                    }
                }
                break;
        }
    }

    else {
        echo'FALSE<$>Please select a value';
    }
} else
    echo'FALSE<$$>Sorry please login to complete the deletion request';
$output = ob_get_clean();
echo $output = trim($output);
?>
