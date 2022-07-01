<?php
$myobj = new opening_stock();
    $ok = true;
    $file = $_FILES['upload']['tmp_name'];
    $handle = fopen($file, "r");
    if ($file == NULL) {
      error(_('Please select a file to import'));
      redirect(page_link_to('admin_export'));
    }
    else {
        $dealer_id = $_SESSION[SESS . 'data']['dealer_id'];
        $pid = $_SESSION[SESS . 'data']['id'];
        $primary_id = $dealer_id.date('Ymdhis');
      while(($filesop = fgetcsv($handle, 1000, ",")) !== false)
        {
          $primary_id = $primary_id+1;
         $product = $filesop[0];
          $batch = $filesop[1];
          $qty = $filesop[2];
          $rate = $filesop[3];
          $mfg = $filesop[4];
          $exp = $filesop[5];
          $csa_id = $filesop[6];
          $company_id = $filesop[7];
          $prate = ($rate*18)/100;
          $pr_rate = $rate - $prate;

          $sql = mysqli_query($dbc,"INSERT INTO `stock`(`product_id`, `batch_no`, `rate`, `person_id`,
              `csa_id`, `dealer_id`, `qty`, `salable_damage`, `nonsalable_damage`, `remaining`, `mfg`, `expire`,
              `date`, `pr_rate`, `company_id`, `action`) VALUES
              ('$product','$batch','$rate','$pid','$csa_id','$dealer_id','$qty','0','0','$qty','$mfg','$exp',NOW(),$pr_rate,'$company_id','1')");
        
     
            $q1 = "INSERT INTO `user_primary_sales_order`(`id`, `order_id`, `dealer_id`, `created_date`, 
            `created_person_id`, `sale_date`, `receive_date`, `date_time`, `company_id`, `ch_date`, 
            `challan_no`, `csa_id`, `action`) VALUES ('$primary_id','$primary_id','$dealer_id',NOW(),
                '$pid',NOW(),NOW(),NOW(),'$company_id',NOW(),'Opening Stock','$csa_id',0)";
           $r1 = mysqli_query($dbc,$q1);
     /////////////////////////////////////PRIMARY SALES ORDER DETAILS//////////////////////////////   
        $q2 = "INSERT INTO `user_primary_sales_order_details`(`id`, `order_id`, `product_id`, `rate`, 
            `quantity`, `scheme_qty`, `purchase_inv`, `mfg_date`, `expiry_date`, `receive_date`, `batch_no`, 
            `pr_rate`, `cases`) VALUES ('$primary_id','$primary_id','$product_id','$rate','$qty','0','$batch',
                '$mfg','$exp',NOW(),'$batch','$pr_rate','1')";
        $r2 = mysqli_query($dbc,$q2);
          
          
          
        }

      if ($sql) {
        echo"<div style='margin-top:100px;margin-left:350px'><b><u>Data Inserted Successfully</u></b></div>";
      } else {
        echo'Sorry! There is some problem in the import file.';
       
        }
    }


?>
