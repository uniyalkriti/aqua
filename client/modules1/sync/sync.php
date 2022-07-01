<?php
//include 'include/conectdb.php';
//$dbc = @mysqli_connect('localhost','root','','dsgroup-dsr') OR die ('could not connect:' .mysqli_connect_error());
//print_r($dbc);
$dealer_id=$_SESSION[SESS.'data']['dealer_id'];
$servername = "8.30.244.74";
$username = "root";
$password = "RootAdmin123";
$dbname="dsgroup-new";
// Create connection
$server = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($server->connect_error) {
    die("Connection failed: " . $server->connect_error);
}else{
    //h1('come on');die;
 $q = "SELECT id,dealer_status FROM dealer WHERE id='$dealer_id' LIMIT 0,1";
 $r_q = mysqli_query($server,$q);	
 $data = mysqli_fetch_assoc($r_q);
            mysqli_query($server, "START TRANSACTION");
            if($data['dealer_status'] == '1')  
            {
                 mysqli_query($dbc, "START TRANSACTION");
               // $dbc=for local server connection
              // $server=for server connection
              //*********************************SYNC SERVER to local (user sales order & details)*******************************************\\
                 //Run on server
                $q_s = "SELECT * FROM user_sales_order WHERE sync_status=1 AND dealer_id='$dealer_id'";
                $r_uso = mysqli_query($server,$q_s);
                $res=  mysqli_num_rows($r_uso);
              // h1($q_s); exit;
                if($res>=1){
                $str = array();
                $order_ids=array();
                while($row = mysqli_fetch_assoc($r_uso)){
                $id=$row['order_id'];
                $order_ids[]=$row['order_id'];
                $str[] = "('$id','$row[order_id]','$row[user_id]','$row[dealer_id]', '$row[location_id]','$row[retailer_id]','$row[company_id]','$row[call_status]','$row[total_sale_value]','$row[discount]','$row[amount]','$row[total_sale_qty]','$row[lat_lng]','$row[mccmnclatcellid]','$row[track_address]','$row[date]','$row[time]','$row[override_status]','$row[order_status]','$row[remarks]','0')";
                }
                $str_data = implode(',' ,$str);
                $q = "INSERT INTO `user_sales_order`(`id`, `order_id`, `user_id`, `dealer_id`, `location_id`, `retailer_id`, `company_id`, `call_status`,`total_sale_value`,`discount`,`amount`,`total_sale_qty`,`lat_lng`,`mccmnclatcellid`,`track_address`,`date`, `time`,`override_status`, `order_status`,`remarks`,`sync_status`)  VALUES $str_data";
                 h1($q); exit;
                $run_q = mysqli_query($dbc , $q); 
                if($run_q){
                     //Run on server
                     $q_usod = "SELECT usod.id as uid,usod.order_id,product_id,rate,quantity,scheme_qty,status FROM user_sales_order_details usod INNER JOIN user_sales_order USING(order_id) WHERE user_sales_order.sync_status=1 AND dealer_id='$dealer_id' ORDER BY usod.order_id ASC";
                  // h1($q_usod); 
					$r_usod = mysqli_query($server,$q_usod);
                    $str_usod=array();
                    while($row_usod = mysqli_fetch_assoc($r_usod)){
                    $str_usod[] = "('$row_usod[uid]','$row_usod[order_id]','$row_usod[product_id]', '$row_usod[rate]','$row_usod[quantity]','$row_usod[scheme_qty]','$row_usod[status]')";
                    }
                    $str_data1 = implode(',' ,$str_usod);
                    $qry = "INSERT INTO `user_sales_order_details`(`id`, `order_id`, `product_id`, `rate`, `quantity`, `scheme_qty`, `status`)  VALUES $str_data1";
                   // h1($qry);exit;
                    $run_qry = mysqli_query($dbc,$qry); 
                 }
                 if($run_qry){
					// echo "ANKUSH"; exit;
                     //Update on server					 
                    $str_update = implode(',' ,$order_ids);
                    $q_update="UPDATE `user_sales_order` SET `sync_status`=0 WHERE order_id IN($str_update)";
					
                    $run_q_update = mysqli_query($server,$q_update);   
                 }
                //*********************************  For Rollback  ****************************************///
                if (!$run_qry) {
                    mysqli_rollback($server);
                    mysqli_rollback($dbc);
                 echo'<span class="awm"><h2>Data not Sync on server please contact to support team1...</h2></span>'; 
                 header('refresh:5 ; url=./index.php');
                  }
                //*********************************  Rollback  End here ***DONE*************************************///
                 
            }

            //*********************************SYNC Server to Local (Retailer)*******************************************\\

                $qr = "SELECT * FROM retailer WHERE sync_status=1 AND dealer_id='$dealer_id'";
                $r_qr = mysqli_query($server,$qr);
                $res_qr=  mysqli_num_rows($r_qr);
                if($res_qr>=1){
                $str_qr = array();
                $retailer_ids=array();
                while($row_qr = mysqli_fetch_assoc($r_qr)){
                $id=$row_qr['id'];
                $retailer_ids[]=$row_qr['id'];
                $str_qr[] = "('$id','$row_qr[name]','$row_qr[dealer_id]','$row_qr[location_id]','$row_qr[company_id]','$row_qr[address]','$row_qr[email]','$row_qr[contact_per_name]','$row_qr[landline]','$row_qr[tin_no]','$row_qr[pin_no]','$row_qr[outlet_type_id]','$row_qr[avg_per_month_pur]','$row_qr[created_on]','$row_qr[created_by_person_id]','$row_qr[status]','0')";
                }
                $str_data_qr = implode(',' ,$str_qr);
                $q_qr = "INSERT INTO `retailer`(`id`, `name`, `dealer_id`, `location_id`, `company_id`, `address`, `email`, `contact_per_name`, `landline`,`tin_no`, `pin_no`, `outlet_type_id`,`avg_per_month_pur`,`created_on`, `created_by_person_id`, `status`, `sync_status`) VALUES $str_data_qr";
              // h1($q_qr);
                $run_qr = mysqli_query($dbc , $q_qr); 
                if($run_qr){
                     //Update on server
                    $qr_ids = implode(',' ,$retailer_ids);
                    $qr_update="UPDATE `retailer` SET `sync_status`=0 WHERE id IN($qr_ids)";
                    $run_qr_update = mysqli_query($server,$qr_update);   
                 }
                 
                  //*********************************  For Rollback  ****************************************///
            if (!$q_qr) {
                      mysqli_rollback($server);
                      mysqli_rollback($dbc);
                 echo'<span class="awm"><h2>Data not Sync on server please contact to support team2</h2></span>'; 
                 header('refresh:5 ; url=./index.php');
                  }
                //*********************************  Rollback  End here ************DONE****************************///
                 

                }
             //*********************************SYNC Local to server (Retailer)*******************************************\\
$qr1local = "SELECT * FROM retailer WHERE sync_status=1 AND dealer_id='$dealer_id'";
                $r_qrlocal = mysqli_query($dbc,$qr1local);
                $res_q1rlocal=  mysqli_num_rows($r_qrlocal);
               // h1($res_q1rlocal);
                if($res_q1rlocal>=1){
                $str_qrlocal = array();
                $retailer_idslocal=array();
                while($row_qrlocal = mysqli_fetch_assoc($r_qrlocal)){
                $id=$row_qrlocal['id'];
				$retailer_idslocal[]=$row_qrlocal['id'];
                $str_qrlocal[] = "('$id','$row_qrlocal[name]','$row_qrlocal[dealer_id]','$row_qrlocal[location_id]','$row_qrlocal[company_id]','$row_qrlocal[address]','$row_qrlocal[email]','$row_qrlocal[contact_per_name]',
				'$row_qrlocal[landline]','$row_qrlocal[other_numbers]','$row_qrlocal[tin_no]','$row_qrlocal[pin_no]','$row_qrlocal[outlet_type_id]','$row_qrlocal[avg_per_month_pur]','$row_qrlocal[lat_long]','$row_qrlocal[mncmcclatcellid]','$row_qrlocal[track_address]','$row_qrlocal[created_on]','$row_qrlocal[created_by_person_id]','$row_qrlocal[status]','0','$row_qrlocal[retailer_status]')";
                }
                $str_data_qrlocal = implode(',' ,$str_qrlocal);
                $q_qrlocal = "INSERT INTO `retailer`(`id`, `name`, `dealer_id`, `location_id`, `company_id`, `address`, `email`, `contact_per_name`, `landline`,`other_numbers`,`tin_no`, `pin_no`, `outlet_type_id`,`avg_per_month_pur`,`lat_long`,`mncmcclatcellid`,`track_address`,`created_on`, `created_by_person_id`, `status`, `sync_status`,`retailer_status`) VALUES $str_data_qrlocal";
            // h1($q_qrlocal); exit;
                $run_qrlocal = mysqli_query($server , $q_qrlocal); 
                }
                
                if($run_qrlocal){
                     //Update on localver
                    $qr_idslocal = implode(',' ,$retailer_idslocal);
                    $qr_updatelocal="UPDATE `retailer` SET `sync_status`=0 WHERE id IN($qr_idslocal)";
                    $run_qr_updatelocal = mysqli_query($dbc,$qr_updatelocal);   
                 }
                //need update
                      //*********************************  For Rollback  ****************************************///
            if (!$r_qrlocal) {
                      mysqli_rollback($server);
                      mysqli_rollback($dbc);
                 echo'<span class="awm"><h2>Data not Sync on localver please contact to support Retailer</h2></span>'; 
                 header('refresh:5 ; url=./index.php');
                  }
               
                //*********************************  Rollback  End here ************DONE****************************///
              //*********************************SYNC SERVER to LOCAL (Retailer)*******************************************\\

                $qr1ser = "SELECT * FROM retailer WHERE sync_status=1 AND dealer_id='$dealer_id'";
                $r_qrser = mysqli_query($server,$qr1ser);
                $res_q1rser=  mysqli_num_rows($r_qrser);
               // h1($res_q1rser);
                if($res_q1rser>=1){
                $str_qrser = array();
                $retailer_idsser=array();
                while($row_qrser = mysqli_fetch_assoc($r_qrser)){
                $id=$row_qrser['id'];
				$retailer_idsser[]=$row_qrser['id'];
                $str_qrser[] = "('$id','$row_qrser[name]','$row_qrser[dealer_id]','$row_qrser[location_id]','$row_qrser[company_id]','$row_qrser[address]','$row_qrser[email]','$row_qrser[contact_per_name]',
				'$row_qrser[landline]','$row_qrser[other_numbers]','$row_qrser[tin_no]','$row_qrser[pin_no]','$row_qrser[outlet_type_id]','$row_qrser[avg_per_month_pur]','$row_qrser[lat_long]','$row_qrser[mncmcclatcellid]','$row_qrser[track_address]','$row_qrser[created_on]','$row_qrser[created_by_person_id]','$row_qrser[status]','0','$row_qrser[retailer_status]')";
                }
                $str_data_qrser = implode(',' ,$str_qrser);
                $q_qrser = "INSERT INTO `retailer`(`id`, `name`, `dealer_id`, `location_id`, `company_id`, `address`, `email`, `contact_per_name`, `landline`,`other_numbers`,`tin_no`, `pin_no`, `outlet_type_id`,`avg_per_month_pur`,`lat_long`,`mncmcclatcellid`,`track_address`,`created_on`, `created_by_person_id`, `status`, `sync_status`,`retailer_status`) VALUES $str_data_qrser";
            // h1($q_qrser); exit;
                $run_qrser = mysqli_query($dbc , $q_qrser); 
                }
                
                if($run_qrser){
                     //Update on server
                    $qr_idsser = implode(',' ,$retailer_idsser);
                    $qr_updateser="UPDATE `retailer` SET `sync_status`=0 WHERE id IN($qr_idsser)";
                    $run_qr_updateser = mysqli_query($server,$qr_updateser);   
                 }
                //need update
                      //*********************************  For Rollback  ****************************************///
            if (!$r_qrser) {
                      mysqli_rollback($server);
                      mysqli_rollback($dbc);
                 echo'<span class="awm"><h2>Data not Sync on server please contact to support Retailer</h2></span>'; 
                 header('refresh:5 ; url=./index.php');
                  }
                //*********************************  Rollback  End here ************DONE****************************///
                
             //*********************************SYNC server to Local (challan Order)*******************************************\\
               $q_ch2 = "SELECT * FROM challan_order WHERE sync_status=1 AND ch_dealer_id='$dealer_id'";
                $r_ch2 = mysqli_query($server,$q_ch2);
                $res_ch2=  mysqli_num_rows($r_ch2);
				
                if($res_ch2>=1){
                $str_ch2 = array();
                $ch_ids2=array();
                while($row_ch2 = mysqli_fetch_assoc($r_ch2)){

                $ch_ids2[]=$row_ch2['id'];
                $str_ch2[] = "('$row_ch2[id]','$row_ch2[ch_no]','$row_ch2[ch_created_by]', '$row_ch2[ch_dealer_id]','$row_ch2[ch_retailer_id]','$row_ch2[dispatch_date]','$row_ch2[ch_date]','$row_ch2[company_id]','$row_ch2[dispatch_status]','$row_ch2[discount]','$row_ch2[sesId]','$row_ch2[remark]
				','0','$row_ch2[invoice_type]','$row_ch2[payment_status]','$row_ch2[isclaim]','$row_ch2[istarget_claim]','$row_ch2[discount_per]'
				,'$row_ch2[discount_amt]','$row_ch2[amount]','$row_ch2[remaining]')";
                }
                $ch_data2 = implode(',' ,$str_ch2);
                $qch2 = "INSERT INTO `challan_order`(`id`, `ch_no`, `ch_created_by`, `ch_dealer_id`, `ch_retailer_id`, `dispatch_date`, `ch_date`,`company_id`,`dispatch_status`,`discount`,`sesId`, `remark`, `sync_status`,`invoice_type`,`payment_status`,`isclaim`
				,`istarget_claim`,`discount_per`,`discount_amt`,`amount`,`remaining`) VALUES $ch_data2";
               // h1($qch2); exit;
                $run_qch2 = mysqli_query($dbc , $qch2); 
                if($run_qch2){
                     //Run on server
                    $q_chd2 = "SELECT chd.id as cid,ch_id,product_id,hsn_code,catalog_details_id,batch_no,tax,vat_amt,
					qty,product_rate,free_qty,order_id,user_id,mrp,cd,cd_type,cd_amt,dis_type,dis_amt,dis_percent,taxable_amt,remain_amount FROM challan_order_details chd INNER JOIN challan_order ch ON ch.id=chd.ch_id WHERE ch.sync_status=1 AND ch_dealer_id='$dealer_id' ORDER BY ch_id ASC";
                    //h1($q_chd2); exit;
					$r_chd2 = mysqli_query($server,$q_chd2);
                    $str_chd2=array();
                    while($row_chd2 = mysqli_fetch_assoc($r_chd2)){
                    $str_chd2[] = "(null,'$row_chd2[ch_id]','$row_chd2[product_id]','$row_chd2[hsn_code]','$row_chd2[catalog_details_id]', '$row_chd2[batch_no]','$row_chd2[tax]','$row_chd2[vat_amt]'
					,'$row_chd2[qty]','$row_chd2[product_rate]','$row_chd2[free_qty]', '$row_chd2[order_id]','$row_chd2[user_id]','$row_chd2[mrp]','$row_chd2[cd]',
					'$row_chd2[cd_type]','$row_chd2[cd_amt]','$row_chd2[dis_type]','$row_chd2[dis_amt]','$row_chd2[dis_percent]',
					'$row_chd2[taxable_amt]','$row_chd2[remain_amount]','0')";
                    }
                    $chd_data2 = implode(',' ,$str_chd2);
					//echo $chd_data2;
                    $qry_chd2 = "INSERT INTO `challan_order_details`(`id`, `ch_id`, `product_id`,`hsn_code`, `catalog_details_id`, `batch_no`, `tax`,`vat_amt`,
					`qty`, `product_rate`, `free_qty`, `order_id`, `user_id`, `mrp`, `cd`, `cd_type`,
					`cd_amt`, `dis_type`, `dis_amt`, `dis_percent`, `taxable_amt`,`remain_amount`,`sync_status`)  VALUES $chd_data2";
                   //  h1($qry_chd2); exit;
                    $run_qry_chd2 = mysqli_query($dbc , $qry_chd2); 
                 }
                 if($run_qry_chd2){
                     //Update on server
                    $ch_update2 = implode(',' ,$ch_ids2);
                    $qch_update2="UPDATE `challan_order` SET `sync_status`=0 WHERE id IN($ch_update2)";
                   // h1($qch_update);
                    $run_qch_update2 = mysqli_query($server,$qch_update2);   
                 }
                 
                 //*********************************  For Rollback  ****************************************///
                if (!$run_qch2) {
                    mysqli_rollback($server);
                    mysqli_rollback($dbc);
                 echo'<span class="awm"><h2>Data not Sync on server please contact to support team chllan</h2></span>'; 
                 header('refresh:5 ; url=./index.php');
                  }
                //*********************************  Rollback  End here ****************DONE************************///
            }    
         //*********************************SYNC Local to server (challan Order)*******************************************\\
                                
             //*********************************SYNC local to Server (challan Order)*******************************************\\
               $q_clocal = "SELECT * FROM challan_order WHERE sync_status=1 AND ch_dealer_id='$dealer_id'";
                $r_clocal = mysqli_query($dbc,$q_clocal);
                $res_clocal=  mysqli_num_rows($r_clocal);
				
                if($res_clocal>=1){
                $str_clocal = array();
                $ch_idsloc=array();
                while($row_clocal = mysqli_fetch_assoc($r_clocal)){

                $ch_idsloc[]=$row_clocal['id'];
                $str_clocal[] = "('$row_clocal[id]','$row_clocal[ch_no]','$row_clocal[ch_created_by]', '$row_clocal[ch_dealer_id]','$row_clocal[ch_retailer_id]','$row_clocal[dispatch_date]','$row_clocal[ch_date]','$row_clocal[company_id]','$row_clocal[dispatch_status]','$row_clocal[discount]','$row_clocal[sesId]','$row_clocal[remark]
				','0','$row_clocal[invoice_type]','$row_clocal[payment_status]','$row_clocal[isclaim]','$row_clocal[istarget_claim]','$row_clocal[discount_per]'
				,'$row_clocal[discount_amt]','$row_clocal[amount]','$row_clocal[remaining]')";
                
				}
                $ch_datalo = implode(',' ,$str_clocal);
                $qclocal = "INSERT INTO `challan_order`(`id`, `ch_no`, `ch_created_by`, `ch_dealer_id`, `ch_retailer_id`, `dispatch_date`, `ch_date`,`company_id`,`dispatch_status`,`discount`,`sesId`, `remark`, `sync_status`,`invoice_type`,`payment_status`,`isclaim`
				,`istarget_claim`,`discount_per`,`discount_amt`,`amount`,`remaining`) VALUES $ch_datalo";
               // h1($qclocal); exit;
                $run_qclocal = mysqli_query($server , $qclocal); 
                if($run_qclocal){
                     //Run on server
                    $q_chdloc = "SELECT chd.id as cid,ch_id,product_id,hsn_code,catalog_details_id,batch_no,tax,vat_amt,
					qty,product_rate,free_qty,order_id,user_id,mrp,cd,cd_type,cd_amt,dis_type,dis_amt,dis_percent,taxable_amt,remain_amount FROM challan_order_details chd INNER JOIN challan_order ch ON ch.id=chd.ch_id WHERE ch.sync_status=1 AND ch_dealer_id='$dealer_id' ORDER BY ch_id ASC";
                   // h1($q_chdloc);
					$r_chdloc = mysqli_query($dbc,$q_chdloc);
                    $str_chdloc=array();
                    while($row_chdloc = mysqli_fetch_assoc($r_chdloc)){
                    $str_chdloc[] = "(null,'$row_chdloc[ch_id]','$row_chdloc[product_id]','$row_chdloc[hsn_code]','$row_chdloc[catalog_details_id]', '$row_chdloc[batch_no]','$row_chdloc[tax]','$row_chdloc[vat_amt]'
					,'$row_chdloc[qty]','$row_chdloc[product_rate]','$row_chdloc[free_qty]', '$row_chdloc[order_id]','$row_chdloc[user_id]','$row_chdloc[mrp]','$row_chdloc[cd]',
					'$row_chdloc[cd_type]','$row_chdloc[cd_amt]','$row_chdloc[dis_type]','$row_chdloc[dis_amt]','$row_chdloc[dis_percent]',
					'$row_chdloc[taxable_amt]','$row_chdloc[remain_amount]','0')";
                    }
                    $chd_dataloc = implode(',' ,$str_chdloc);
					//echo $chd_dataloc;
                    $qry_chdloc = "INSERT INTO `challan_order_details`(`id`, `ch_id`, `product_id`,`hsn_code`, `catalog_details_id`, `batch_no`, `tax`,`vat_amt`,
					`qty`, `product_rate`, `free_qty`, `order_id`, `user_id`, `mrp`, `cd`, `cd_type`,
					`cd_amt`, `dis_type`, `dis_amt`, `dis_percent`, `taxable_amt`,`remain_amount`,`sync_status`)  VALUES $chd_dataloc";
                   //  h1($qry_chdloc); exit;
                    $run_qry_chdloc = mysqli_query($server , $qry_chdloc); 
                 }
                 if($run_qry_chdloc){
                     //Update on server
                    $ch_updatelo = implode(',' ,$ch_idsloc);
                    $qch_updatelo="UPDATE `challan_order` SET `sync_status`=0 WHERE id IN($ch_updatelo)";
                   // h1($qch_updatelo);
                    $run_qch_updatelo = mysqli_query($dbc,$qch_updatelo);   
                 }
                 
                 //*********************************  For Rollback  ****************************************///
                if (!$run_qclocal) {
                    mysqli_rollback($server);
                    mysqli_rollback($dbc);
                 echo'<span class="awm"><h2>Data not Sync on server please contact to support team4</h2></span>'; 
                 header('refresh:5 ; url=./index.php');
                  }
                //*********************************  Rollback  End here ****************************************///
                //*********************************  Rollback  End here **************DONE**************************///
            }
                 
//*********************************SYNC Local to server (Primary Sale Order)*******************************************\\
                $q_pso = "SELECT * FROM user_primary_sales_order WHERE sync_status=1 AND dealer_id='$dealer_id'";
                $r_pso = mysqli_query($dbc,$q_pso);
                $res_pso=  mysqli_num_rows($r_pso);
				
                if($res_pso>=1){
                $str_pso = array();
                $pso_ids=array();
                while($row_pso = mysqli_fetch_assoc($r_pso)){

                $pso_ids[]=$row_pso['id'];
                $str_pso[] = "('$row_pso[id]$dealer_id','$row_pso[order_id]$dealer_id','$row_pso[dealer_id]', '$row_pso[created_date]','$row_pso[created_person_id]','$row_pso[sale_date]','$row_pso[receive_date]','$row_pso[date_time]','$row_pso[company_id]','$row_pso[ch_date]','$row_pso[challan_no]','$row_pso[ss_id]','0')";
                }
                $pso_data = implode(',' ,$str_pso);
                $qpso = "INSERT INTO `user_primary_sales_order`(`id`, `order_id`, `dealer_id`, `created_date`, `created_person_id`, `sale_date`, `receive_date`, `date_time`, `company_id`, `ch_date`, `challan_no`, `ss_id`, `sync_status`) VALUES $pso_data";
             //   h1($qpso);
                $run_pso = mysqli_query($server , $qpso); 
                if($run_pso){
                     //Run on server
                    $q_psod = "SELECT upsod.id as uid,upsod.order_id,product_id,rate,quantity,scheme_qty,purchase_inv,mfg_date,expiry_date,upsod.receive_date,batch_no,pr_rate FROM user_primary_sales_order_details upsod
					 INNER JOIN user_primary_sales_order USING(order_id) WHERE sync_status=1 AND dealer_id='$dealer_id' ORDER BY upsod.order_id ASC";
                    //h1($q_psod);
					$r_psod = mysqli_query($dbc,$q_psod);
                    $str_psod=array();
                    while($row_psod = mysqli_fetch_assoc($r_psod)){
                    $str_psod[] = "('$row_psod[uid]$dealer_id','$row_psod[order_id]$dealer_id','$row_psod[product_id]','$row_psod[rate]', '$row_psod[quantity]','$row_psod[scheme_qty]','$row_psod[purchase_inv]','$row_psod[mfg_date]',"
                            . "'$row_psod[expiry_date]', '$row_psod[receive_date]','$row_psod[batch_no]','$row_psod[pr_rate]')";
                    }
                    $psod_data = implode(',' ,$str_psod);
                    $qry_psod = "INSERT INTO `user_primary_sales_order_details`(`id`, `order_id`, `product_id`, `rate`, `quantity`, `scheme_qty`, `purchase_inv`, `mfg_date`, `expiry_date`, `receive_date`, `batch_no`, `pr_rate`) VALUES $psod_data";
                   //  h1($qry_psod);
                    $run_qry_psod = mysqli_query($server , $qry_psod); 
                 }
                 if($run_qry_psod){
                     //Update on server
                    $pso_update = implode(',' ,$pso_ids);
                    $qpso_update="UPDATE `user_primary_sales_order` SET `sync_status`=0 WHERE order_id IN($pso_update)";
                   // h1($qpso_update);
                    $run_qpso_update = mysqli_query($dbc,$qpso_update);   
                 }
                 
                 //*********************************  For Rollback  ****************************************///
                if (!$r_pso) {
                    mysqli_rollback($server);
                    mysqli_rollback($dbc);
                 echo'<span class="awm"><h2>Data not Sync on server please contact to support team5</h2></span>'; 
                 //header('refresh:5 ; url=./index.php');e
                  }
                //*********************************  Rollback  End here ****************************************///

                  //*********************************SYNC Local to server (DISPATCH)*******************************************\\
               $ddq_ch = "SELECT * FROM daily_dispatch WHERE sync_status=1 AND dealer_id='$dealer_id'";
              
                $ddr_ch = mysqli_query($dbc,$ddq_ch);
               // print_r($ddr_ch);
                $res_dd=  mysqli_num_rows($ddr_ch);
		 if($res_dd>=1){
                $str_dd = array();
                $dd_ids=array();
                while($row_dd = mysqli_fetch_assoc($ddr_ch)){

                $dd_ids[]=$row_dd['dispatch_id'];
                $str_dd[] = "('$row_dd[dispatch_id]','$row_dd[dispatch_no]','$row_dd[dealer_id]', '$row_dd[van_no]','$row_dd[dispatch_date]','$row_dd[total_bills]','$row_dd[total_product]','$row_dd[company_id]','$row_dd[created_by]','0')";
               // print_r($dd_ids);exit;
                
                }
                $dd_data = implode(',' ,$str_dd);
               
                $qdd = "INSERT INTO `daily_dispatch`(`dispatch_id`, `dispatch_no`, `dealer_id`, `van_no`, `dispatch_date`, `total_bills`, `total_product`, `company_id`, `created_by`, `sync_status`) VALUES $dd_data";
              //  h1($qdd);exit;
                $run_qdd = mysqli_query($server , $qdd); 
              ///  print_r($run_qch); exit;
                if($run_qdd){
                     //Run on server
                    $q_ddd = "SELECT dd_d.dispatch_id as dd_disid,dd_d.ch_id,dd_d.sortorder FROM daily_dispatch_details dd_d INNER JOIN daily_dispatch dd ON dd.dispatch_id=dd_d.dispatch_id WHERE dd.sync_status=1 AND dealer_id='$dealer_id' ORDER BY ch_id ASC";
                    //h1($q_ddd);exit;
                    $r_ddd = mysqli_query($dbc,$q_ddd);
                    $str_dd_d=array();
                    while($row_ddd = mysqli_fetch_assoc($r_ddd)){
                    $str_dd_d[] = "('$row_ddd[dd_disid]','$row_ddd[ch_id]','$row_ddd[sortorder]')";
                    }
                    $dd_data = implode(',' ,$str_dd_d);
                    $qry_dd = "INSERT INTO `daily_dispatch_details`(`dispatch_id`, `ch_id`, `sortorder`)  VALUES $dd_data";
                    // h1($qry_chd);exit;
                    $run_qry_dd = mysqli_query($server , $qry_dd); 
                 }
                 if($run_qry_dd){
                     //Update on server
                    $dd_update = implode(',' ,$dd_ids);
                    $qdd_update="UPDATE `daily_dispatch` SET `sync_status`=0 WHERE dispatch_id IN($dd_update)";
                   // h1($qch_update);
                    $run_qdd_update = mysqli_query($dbc,$qdd_update);   
                 }
                 
                 //*********************************  For Rollback  ****************************************/// 
                if (!$run_qdd) {
                    mysqli_rollback($server);
                    mysqli_rollback($dbc);
                 echo'<span class="awm"><h2>Data not Sync on server please contact to support dispatch</h2></span>'; 
                 header('refresh:5 ; url=./index.php');
                  }
                //*********************************  Rollback  End here ****************************************///
            }
            
            //*********************************SYNC Local to server (PAYMENT)*******************************************\\
               $q_pm = "SELECT * FROM payment_collection  WHERE sync_status=1 AND dealer_id='$dealer_id'";
              
                $r_pm = mysqli_query($dbc,$q_pm);
               // print_r($ddr_ch);
                $res_pm=  mysqli_num_rows($r_pm);
		 if($res_pm>=1){
                $str_pm = array();
                $pm_ids=array();
                while($row_pm = mysqli_fetch_assoc($r_pm)){

                $pm_ids[]=$row_pm['id'];
                $str_pm[] = "('$row_pm[dealer_id]','$row_pm[challan_id]','$row_pm[retailer_id]', '$row_pm[total_amount]','$row_pm[pay_mode]','$row_pm[bank_name]','$row_pm[chq_no]','$row_pm[chq_date]','$row_pm[Remark]','$row_pm[pay_date_time]','0')";
               // print_r($dd_ids);exit;
                
                }
                $pm_data = implode(',' ,$str_pm);
               
                $qpm = "INSERT INTO `payment_collection`(`dealer_id`, `challan_id`, `retailer_id`, `total_amount`, `pay_mode`, `bank_name`, `chq_no`, `chq_date`, `Remark`, `pay_date_time`, `sync_status`) VALUES $pm_data";
              // h1($qpm);exit;
                $run_qpm = mysqli_query($server,$qpm); 
              ///  print_r($run_qch); exit;
             
                 if($run_qpm){
                     //Update on server
                    $pm_update = implode(',' ,$pm_ids);
                    $qpm_update="UPDATE `payment_collection` SET `sync_status`=0 WHERE id IN($pm_update)";
                   // h1($qch_update);
                    $run_qpm_update = mysqli_query($dbc,$qpm_update);   
                 }
                 
                 //*********************************  For Rollback  ****************************************/// 
                if (!$run_qpm) {
                    mysqli_rollback($server);
                    mysqli_rollback($dbc);
                 echo'<span class="awm"><h2>Data not Sync on server please contact to support PAYMENT</h2></span>'; 
                 header('refresh:5 ; url=./index.php');
                  }
                //*********************************  Rollback  End here ****************************************///
            }
            
             //*********************************SYNC Local to server (DAMAGE ORDER)*******************************************\\
               $q_do = "SELECT * FROM damage_order WHERE sync_status=1 AND ch_dealer_id='$dealer_id'";
             // echo $q_do; exit;
                $qr_do = mysqli_query($dbc,$q_do);
               // print_r($ddr_ch);
                $res_do=  mysqli_num_rows($qr_do);
		 if($res_do>=1){
                $str_do = array();
                $do_ids=array();
                while($row_do = mysqli_fetch_assoc($qr_do)){

                $do_ids[]=$row_do['id'];
                $str_do[] = "('$row_do[id]','$row_do[ch_created_by]','$row_do[ch_dealer_id]', '$row_do[ch_retailer_id]','$row_do[dispatch_date]','$row_do[ch_date]','$row_do[company_id]'
                    ,'$row_do[dispatch_status]','$row_do[sesId]','$row_do[remark]','$row_do[complaint_id]','$row_do[actual_amount]','$row_do[saleable_non_saleable]','0')";
               // print_r($dd_ids);exit;
                
                }
                $do_data = implode(',' ,$str_do);
               
                $qdo = "INSERT INTO `damage_order`(`id`, `ch_created_by`, `ch_dealer_id`, `ch_retailer_id`, 
                    `dispatch_date`, `ch_date`, `company_id`, `dispatch_status`, `sesId`, `remark`, `complaint_id`,
                    `actual_amount`, `saleable_non_saleable`, `sync_status`) VALUES $do_data";
               // h1($qdo);exit;
                $run_qdo = mysqli_query($server , $qdo); 
              ///  print_r($run_qch); exit;
                if($run_qdo){
                     //Run on server
                    $q_dod = "SELECT dod.ch_id as doid, `product_id`, `catalog_details_id`, `batch_no`,
                        `tax`, `qty`, `product_rate`, `free_qty`, `order_id`, `user_id`, `mrp`, `cd`,
                        `cd_type`, `cd_amt`, `dis_type`, `dis_amt`, `dis_percent`, `taxable_amt`, dod.`actual_amount`,
                        `replace_product_id`, `replace_mrp`, `replace_rate`, `replace_quantity`, `replace_amount` FROM
                        `damage_order_details` dod INNER JOIN damage_order do ON do.id=dod.ch_id
                        WHERE do.sync_status=1 AND do.ch_dealer_id='$dealer_id' ORDER BY dod.id ASC";
                   // h1($q_dod);exit;
                    $r_dod = mysqli_query($dbc,$q_dod);
                    $str_do_d=array();
                    while($row_dod = mysqli_fetch_assoc($r_dod)){
                    $str_do_d[] = "('$row_dod[doid]','$row_dod[product_id]','$row_dod[catalog_details_id]',
                        '$row_dod[batch_no]','$row_dod[tax]','$row_dod[qty]','$row_dod[product_rate]','$row_dod[free_qty]',
                            '$row_dod[order_id]','$row_dod[user_id]','$row_dod[mrp]','$row_dod[cd]','$row_dod[cd_type]',
                               '$row_dod[cd_amt]','$row_dod[dis_type]','$row_dod[dis_amt]','$row_dod[dis_percent]',
                                   '$row_dod[taxable_amt]','$row_dod[actual_amount]','$row_dod[replace_product_id]',
                                    '$row_dod[replace_mrp]','$row_dod[replace_rate]','$row_dod[replace_quantity]'
                                        ,'$row_dod[replace_amount]')";
                    }
                    $dod_data = implode(',' ,$str_do_d);
                    $qry_dod = "INSERT INTO `damage_order_details`(`ch_id`, `product_id`, `catalog_details_id`,
                        `batch_no`, `tax`, `qty`, `product_rate`, `free_qty`, `order_id`, `user_id`, `mrp`, `cd`,
                        `cd_type`, `cd_amt`, `dis_type`, `dis_amt`, `dis_percent`, `taxable_amt`, `actual_amount`,
                        `replace_product_id`, `replace_mrp`, `replace_rate`, `replace_quantity`, `replace_amount`)
                        VALUES $dod_data";
                   // h1($qry_dod);exit;
                    $run_qry_dod = mysqli_query($server , $qry_dod); 
                 }
                 if($run_qry_dod){
                     //Update on server
                    $dod_update = implode(',' ,$do_ids);
                    $qdod_update="UPDATE `damage_order` SET `sync_status`=0 WHERE id IN($dod_update)";
                    h1($qdod_update);
                    $run_qod_update = mysqli_query($dbc,$qdod_update);   
                 }
                 
                 //*********************************  For Rollback  ****************************************/// 
                if (!$run_qry_dod) {
                    mysqli_rollback($server);
                    mysqli_rollback($dbc);
                 echo'<span class="awm"><h2>Data not Sync on server please contact to support DAMAGe</h2></span>'; 
                 header('refresh:5 ; url=./index.php');
                  }
                //*********************************  Rollback  End here ****************************************///
            }
            
                 
				 
				}
                                
                         
                                
               echo'<span class="grn_msg"><h2>Data Sync Successfully on Server</h2></span>'; 
              header('refresh:5 ; url=./index.php');
              mysqli_commit($server);
              mysqli_commit($dbc);
              
            }
            else{
                // DROP CLIENT DB (LOCAL)
                //$link=new mysqli($servername, $username, $password);
                 $drop = "DROP DATABASE `$dbname`";
                // h1($drop);
                // $r_drop = mysqli_query($dbc,$drop);
             echo'<span class="awm">Your account deactivated</span>';

                 }
                          
}

?>

