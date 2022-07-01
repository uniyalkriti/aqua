<?php

// This class will handle all the task related to packing slip in and bill of packing slips
class import_os extends myfilter {

    public $poid = NULL;

    public function __construct() {
        parent::__construct();
    }

   public function import_opening_stock_save() {
        global $dbc;

        if ($_FILES["excelFile"]["error"] > 0) {
            echo "Error: " . $_FILES["excelFile"]["error"] . "<br>";
        } else {        
            $a = $_FILES["excelFile"]["tmp_name"];        
            $csv_file = $a;
            $dealer_id=$_SESSION[SESS.'data']['dealer_id'];
            $wherec="id=".$dealer_id;
            $csa_id=myrowval('dealer','csa_id',$wherec);
            if (($getfile = fopen($csv_file, "r")) !== FALSE) {
                $data = fgetcsv($getfile, 1000, ",");
                 $inum=2;
                 $ch_data=array();
                    mysqli_autocommit($dbc, FALSE);
                    mysqli_query($dbc, "START TRANSACTION");
                while (($data = fgetcsv($getfile, 1000, ",")) !== FALSE) {
                   
                    $curr_date = date('Y-m-d');
                    $result = $data;
                    $result1  = str_replace(",","",$result);  
                    
                    $str = implode(",", $result1);
                    $slice = explode(",", $str);

                   
                    $sno=$slice[0];
                    $item_code = $slice[1];
                    //h1($item_code);
                    $wherep="itemcode=".$item_code;
                    $product_id=myrowval('catalog_product','id',$wherep);
                    $product_name = $slice[2];
                    $mrp = $slice[3];
                    $rate = $slice[4];
                    $qty = $slice[5];
                    $batch_no = $slice[6];
                    $mfg_date1 = $slice[7];
                    $date = str_replace('/', '-', $mfg_date1);
                    $mfg_date = date("Y-m-d", strtotime($mfg_date1));
                    $ch_data[$item_code]=$item_code;
                     if(empty($item_code)){
                      $error_log="Item Code ($item_code) Not Match";  
                      return array('status' =>'false', 'myreason' => "Serial number <b> $inum </b><br>$error_log");
                      }
					  else{
             if(!empty($mrp) && !empty($rate) && !empty($qty) && !empty($batch_no) && !empty($mfg_date) && !empty($product_id)){
                    $query="INSERT IGNORE `opening_stocks`(`product_id`, `batch_no`, `rate`, `dealer_rate`, `mrp`, `person_id`, `csa_id`, `dealer_id`, `qty`, `salable_damage`, `nonsalable_damage`, `remaining`, `mfg`, `expire`, `date`, `pr_rate`, `company_id`, `action`, `sync_status`, `update_date_time`) VALUES ('$product_id','$batch_no','$rate','$rate','$mrp','$dealer_id','$csa_id','$dealer_id','$qty','0','0','$qty','$mfg_date','$mfg_date',NOW(),$rate,'1','1','1',NOW())";  
                  // h1($query);
                   $run_query = mysqli_query($dbc, $query);   
                   
                    if(!$run_query) {
                        mysqli_rollback($dbc);//exit;
                        return array('status' =>'false', 'myreason' => "Serial number <b> $inum </b> <br>$error_log <br> Opening Stock table error");
                    }else{
                   
                       $query_details="INSERT IGNORE `stock`(`product_id`, `batch_no`, `rate`, `dealer_rate`, `mrp`, `person_id`, `csa_id`, `dealer_id`, `qty`, `salable_damage`, `nonsalable_damage`, `remaining`, `mfg`, `expire`, `date`, `pr_rate`, `company_id`, `action`, `sync_status`, `update_date_time`) VALUES ('$product_id','$batch_no','$rate','$rate','$mrp','$dealer_id','$csa_id','$dealer_id','$qty','0','0','$qty','$mfg_date','$mfg_date',NOW(),$rate,'1','1','1',NOW())";
                     // h1($query_details);
                       $run_query_details= mysqli_query($dbc, $query_details);                
                   }
                }
            }
                   $inum++;
                   
                }
                 //exit;
                 mysqli_commit($dbc);
                 return array('status' =>'true', 'myreason' => 'Opening Stock imported successfully', 'ItemCode' => $item_code);
               
            }
        }
        
    }

}

// class end here
?>
