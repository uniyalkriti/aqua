<?php
class dashboard extends myfilter {

    public $id = NULL;
    public $id_detail = NULL;

    public function __construct($id = NULL) {
        parent::__construct();
        $this->id = $id;
    }

       ######################################## TRADE PO Starts here ####################################################	

    public function available_stock_welcome($filter = '', $records = '', $orderby = '') {
        global $dbc;
        $filterstr = $this->oo_filter($filter, $records, $orderby);
        
       $q = "SELECT cp.id,cp.name,(SUM(remaining))as stock from catalog_product cp INNER JOIN stock upsod ON cp.id = upsod.product_id INNER JOIN stock USING(order_id)  $filterstr GROUP BY cp.id ORDER BY stock ASC LIMIT 10 ";
        $r = mysqli_query($dbc, $q);
        while ($row = mysqli_fetch_array($r)) {
            $id = $row['id'];
            $out[$id]['name'] = $row['name'];
            $stock = $row['stock'];
            
            $q = "select SUM(qty+free_qty) as sale from challan_order co INNER JOIN challan_order_details cod ON co.id = cod.ch_id where product_id =$row[id] AND ch_dealer_id = ".$_SESSION[SESS.'data']['dealer_id']." LIMIT 1 ";
            $res = mysqli_query($dbc, $q);
            $row2 = mysqli_fetch_array($res);
            $sold = $row2['sale'];
             $out[$id]['stock']  = $stock - $sold;
        }
        return $out;
    }
    
    ######################################## TRADE PO Starts here ####################################################	

    public function available_stock($filter = '', $records = '', $orderby = '') {
        global $dbc;
        $filterstr = $this->oo_filter($filter, $records, $orderby);
        
       $q = "SELECT cp.id,cp.name,(SUM(quantity+scheme_qty))as stock from catalog_product cp INNER JOIN user_primary_sales_order_details upsod ON cp.id = upsod.product_id INNER JOIN user_primary_sales_order USING(order_id)  $filterstr GROUP BY cp.id ORDER BY stock ASC LIMIT 10 ";
        $r = mysqli_query($dbc, $q);
        while ($row = mysqli_fetch_array($r)) {
            $id = $row['id'];
            $out[$id]['name'] = $row['name'];
            $stock = $row['stock'];
            
            $q = "select SUM(qty+free_qty) as sale from challan_order co INNER JOIN challan_order_details cod ON co.id = cod.ch_id where product_id =$row[id] AND ch_dealer_id = ".$_SESSION[SESS.'data']['dealer_id']." LIMIT 1 ";
            $res = mysqli_query($dbc, $q);
            $row2 = mysqli_fetch_array($res);
            $sold = $row2['sale'];
             $out[$id]['stock']  = $stock - $sold;
        }
        return $out;
    }

    public function expiry_batch($filter = '', $records = '', $orderby = '') {
        global $dbc;
        $cur_date = date('Ym');
        $filterstr = $this->oo_filter($filter, $records, $orderby);
        $q = "SELECT upsod.id ,expiry_date,cp.name,batch_no from catalog_product cp INNER JOIN user_primary_sales_order_details upsod ON cp.id = upsod.product_id INNER JOIN user_primary_sales_order USING(order_id) $filterstr AND DATE_FORMAT(expiry_date,'%Y%m') = '$cur_date' ORDER BY expiry_date ASC  ";
        $r = mysqli_query($dbc, $q);
        while ($row = mysqli_fetch_array($r)) {
            $id = $row['id'];
            $out[$id]['name'] = $row['name'];
            $out[$id]['batch_no'] = $row['batch_no'];
        }
        return $out;
    }
    
    public function dealer_profile($dealerId) {
        global $dbc;    
        $q = "SELECT dpId,person_name,uname,email,phone,dealer_id,profile_pic from dealer_person_login where dealer_id='$dealerId'";
       //h1($q);
        $r = mysqli_query($dbc, $q);
        while ($row = mysqli_fetch_array($r)) {
            $id = $row['dpId'];
            $out[$id]['dpId'] = $row['dpId'];
            $out[$id]['person_name'] = $row['person_name'];
            $out[$id]['uname'] = $row['uname'];
            $out[$id]['email'] = $row['email'];
            $out[$id]['phone'] = $row['phone'];
            $out[$id]['dealer_id'] = $row['dealer_id'];
            $out[$id]['profile_pic'] = $row['profile_pic'];
        }
        return $out;
    }
    
    public function current_scheme() {
        //pre($_SESSION);
        $state = $_SESSION[SESS.'data']['state_id'];
        global $dbc;   
        $date = date('Y-m-d');
        $q = "SELECT svp.id,svp.value,svp.value_to,svp.scheme_gift,svpd.start_date,svpd.end_date from scheme_value_product_details svp INNER JOIN scheme_value svpd ON svpd.scheme_id = svp.scheme_id where '$date' BETWEEN svpd.start_date AND svpd.end_date AND user = 2 AND state_id = $state";
        $r = mysqli_query($dbc, $q);
        while ($row = mysqli_fetch_array($r)) {
            $id = $row['id'];   
            $start = $row['start_date'];
            $end = $row['end_date'];
            $out[$id]['value'] = $row['value'];
            $out[$id]['value_to'] = $row['value_to'];
            $out[$id]['scheme_gift'] = $row['scheme_gift'];
            $out[$id]['start_date'] = $row['start_date'];
            $out[$id]['end_date'] = $row['end_date'];
            $out[$id]['achieved'] = $this->achieved($start,$end);
            
        }
        //pre($out);
        return $out;
    }
     /////////////////////////////////////////////////////////////////////////////////////
    public function achieved($start,$end)
    {
        global $dbc;
$dealerId = $_SESSION[SESS.'data']['dealer_id'];
        $q= "SELECT SUM(rate*(quantity)) as achieved FROM `user_primary_sales_order` upso INNER JOIN user_primary_sales_order_details upsod ON upsod.order_id = upso.order_id where is_claim=0 AND action=1 AND ch_date >='$start' AND ch_date<='$end' AND dealer_id = $dealerId";
       // h1($q);
        $r = mysqli_query($dbc,$q);
        $row = mysqli_fetch_assoc($r);
        $unit = $row['achieved'];
        return $unit;
        //return 0;     
    }
       ////////////////////////////////////MTD FILTER////////////////////////////////////////////////////
       
    public function get_mtd_list($filter = '', $records = '', $orderby = '') {
        global $dbc;
       // pre($filter);
        $date = $filter[0];
      //  echo $date;
        $dealer = $filter[1];
        $out = array();
        $out['totalinvoice'] = $this->mtd_totalinvoice($date,$dealer);
        $out['retailerreach'] = $this->retailerreach($date,$dealer);
        $out['received'] = $this->mtd_received($date,$dealer);
        $out['payment'] = $this->mtd_payment($date,$dealer);
        //pre($out); //exit;             
       return $out;
      //  echo"ANKUSH";
    }
     ///////////////////////////////TOTAL MTD Payment//////////////////////////////////////////////////////
    public function mtd_payment($month,$dealer)
    {
        global $dbc;
        $out = array();
       // echo $month;
        $q= "SELECT SUM(total_amount) as amount FROM `payment_collection` 
            where DATE_FORMAT(pay_date_time,'%Y-%m') ='$month' AND dealer_id = '$dealer'";
        //h1($q);
        $r = mysqli_query($dbc,$q);
        $row = mysqli_fetch_assoc($r);
        $out['pamtgraph'] = $row['amount'];
        $out['pamount'] = number_format($row['amount'], 2, '.', ',');
       
        $pre_mth = Date("Y-m", strtotime($month . " last month"));
       // echo $pre_mth;
        $qp= "SELECT SUM(total_amount) as amountp FROM `payment_collection` 
            where DATE_FORMAT(pay_date_time,'%Y-%m') ='$pre_mth' AND dealer_id = '$dealer'";
       // h1($qp);
        $rp = mysqli_query($dbc,$qp);
        $rowp = mysqli_fetch_assoc($rp);
        
         $out['pamtpgraph'] = $rowp['amountp'];
         $out['pamountp'] = number_format($rowp['amountp'], 2, '.', ',');
       
      //  $out['remaining'] = money_format('%!i', $unit1);
       // pre($out);
        return $out;  
                        
         
      //  h1($q);
        $r = mysqli_query($dbc,$q);
        $row = mysqli_fetch_assoc($r);
        $unit = $row['amount'];
       // setlocale(LC_MONETARY, 'en_IN');
        //$out['this'] = money_format('%!i', $unit);
        $out['this'] = number_format($unit, 2, '.', ',');
       // pre($out);
        return $out;
    
    }
    
    ///////////////////////////////TOTAL MTD Received//////////////////////////////////////////////////////
    public function mtd_received($month,$dealer)
    {
        global $dbc;
        $out = array();
       // echo $month;
        $q= "SELECT SUM(rate*(quantity)) as amount FROM `user_primary_sales_order` 
             upso INNER JOIN user_primary_sales_order_details upsod ON upsod.order_id = upso.order_id 
             where DATE_FORMAT(ch_date,'%Y-%m') ='$month' AND dealer_id = '$dealer'";
        //h1($q);
        $r = mysqli_query($dbc,$q);
        $row = mysqli_fetch_assoc($r);
        $out['ramtgraph'] = $row['amount'];
        $out['ramount'] = number_format($row['amount'], 2, '.', ',');
       
        $pre_mth = Date("Y-m", strtotime($month . " last month"));
       // echo $pre_mth;
        $qp= "SELECT SUM(rate*(quantity)) as amountp FROM `user_primary_sales_order` 
             upso INNER JOIN user_primary_sales_order_details upsod ON upsod.order_id = upso.order_id 
             where DATE_FORMAT(ch_date,'%Y-%m') ='$pre_mth' AND dealer_id = '$dealer'";
       // h1($qp);
        $rp = mysqli_query($dbc,$qp);
        $rowp = mysqli_fetch_assoc($rp);
        
         $out['ramtpgraph'] = $rowp['amountp'];
         $out['ramountp'] = number_format($rowp['amountp'], 2, '.', ',');
       
      //  $out['remaining'] = money_format('%!i', $unit1);
       // pre($out);
        return $out;  
                        
         
      //  h1($q);
        $r = mysqli_query($dbc,$q);
        $row = mysqli_fetch_assoc($r);
        $unit = $row['amount'];
       // setlocale(LC_MONETARY, 'en_IN');
        //$out['this'] = money_format('%!i', $unit);
        $out['this'] = number_format($unit, 2, '.', ',');
       // pre($out);
        return $out;  
    }
    
  ///////////////////////////////TOTAL MTD INVOICE//////////////////////////////////////////////////////
    public function mtd_totalinvoice($month,$dealer)
    {
        global $dbc;
        $out = array();
       // echo $month;
        $q= "SELECT SUM(amount) as amount, SUM(remaining) as remaining FROM `challan_order` 
              where DATE_FORMAT(ch_date,'%Y-%m') ='$month' AND ch_dealer_id = '$dealer'";
        //h1($q);
        $r = mysqli_query($dbc,$q);
        $row = mysqli_fetch_assoc($r);
         $out['month'] = $month;
         $out['amtgraph'] = $row['amount'];
        $out['amount'] = number_format($row['amount'], 2, '.', ',');
        $out['remaining'] = number_format($row['remaining'], 2, '.', ',');
        $pre_mth = Date("Y-m", strtotime($month . " last month"));
       // echo $pre_mth;
         $qp= "SELECT SUM(amount) as amountp, SUM(remaining) as remainingp FROM `challan_order` 
              where DATE_FORMAT(ch_date,'%Y-%m') ='$pre_mth' AND ch_dealer_id = '$dealer'";
       // h1($qp);
        $rp = mysqli_query($dbc,$qp);
        $rowp = mysqli_fetch_assoc($rp);
        $out['pmonth'] = $pre_mth;
         $out['amtpgraph'] = $rowp['amountp'];
         $out['amountp'] = number_format($rowp['amountp'], 2, '.', ',');
        $out['remainingp'] = number_format($rowp['remainingp'], 2, '.', ',');
      //  $out['remaining'] = money_format('%!i', $unit1);
       // pre($out);
        return $out;  
    }
    
     ///////////////////////////////TOTAL MTD INVOICE//////////////////////////////////////////////////////
    public function retailerreach($month,$dealer)
    {
        global $dbc;
        $out = array();
          $total = 0;
          $totalp = 0;
          $totalnp = 0;
       // echo $month;
        $q= "SELECT count(id) as total FROM `user_sales_order` 
              where DATE_FORMAT(date,'%Y-%m') ='$month' AND dealer_id = '$dealer' GROUP BY date,retailer_id";
       // h1($q);
      
        $r = mysqli_query($dbc,$q);
        while($row = mysqli_fetch_assoc($r))
        {
            $total = $total+$row['total'];
        } 
         $out['totalretailer'] = $total;
        
        $pre_mth = Date("Y-m", strtotime($month . " last month"));
      
         $qp= "SELECT count(id) as totalp FROM `user_sales_order` 
              where DATE_FORMAT(date,'%Y-%m') ='$month' AND dealer_id = '$dealer' AND call_status = '1' GROUP BY date,retailer_id";
    
        $rp = mysqli_query($dbc,$qp);
       while($rowp = mysqli_fetch_assoc($rp))
        {
            $totalp = $totalp+$rowp['totalp'];
        } 
         $out['totalp'] = $totalp;
         
        $qnp= "SELECT count(id) as totalnp FROM `user_sales_order` 
              where DATE_FORMAT(date,'%Y-%m') ='$month' AND dealer_id = '$dealer' AND call_status = '0' GROUP BY date,retailer_id";
    
        $rnp = mysqli_query($dbc,$qnp);
        while($rownp = mysqli_fetch_assoc($rnp))
        {
            $totalnp = $totalnp+$rownp['totalnp'];
        } 
         $out['totalnp'] = $totalnp;
       // pre($out);
        return $out;  
    }
       ///////////////////////////////FOCUS PRODUCT//////////////////////////////////////////////////////
    public function focus_product()
    {
        global $dbc;
        $out = array();
        $date = date("Y-m-d");
        $yest =date('Y-m-d',strtotime("-1 days"));
       // echo $yest;
        $dealerId = $_SESSION[SESS.'data']['dealer_id'];
        $q= "SELECT product_id,sum(qty) as qty, product_rate as rate FROM challan_order_details cod INNER JOIN challan_order
            ON challan_order.id = cod.ch_id WHERE challan_order.ch_dealer_id=$dealerId AND DATE_FORMAT(ch_date,'%Y-%m-%d')='$yest' GROUP BY product_id";
       // h1($q);
        $r = mysqli_query($dbc,$q);
        while ($row = mysqli_fetch_array($r)) {
            $id = $row['product_id'];
           $out[$id]['qty'] = $row['qty'];
            $out[$id]['rate'] = $row['rate'];
            $product = $row['product_id'];
             $where = 'id = '.$row['product_id'];
            $out[$id]['product_name'] = myrowval('catalog_product', 'name',$where);
                      
        }
     //  pre($out);
        return $out;
    }
   
    /////////////////////////////TARGET FOCUS PRODUCT/////////////////////////////////////
      public function target_focus() {
        //pre($_SESSION);
        $state = $_SESSION[SESS.'data']['state_id'];
        $dealer_id = $_SESSION[SESS.'data']['dealer_id'];
        global $dbc;   
        $date = date('Y-m-d');
        $q = "SELECT * FROM `focus_product_target` WHERE `dealer_id`='$dealer_id' AND
            `start_date` <= '$date' AND `end_date` >= '$date'";
       // h1($q);
        $r = mysqli_query($dbc, $q);
        while ($row = mysqli_fetch_array($r)) {
            $id = $row['id'];
           $out[$id]['start_date'] = $row['start_date'];
            $out[$id]['end_date'] = $row['end_date'];
            $product = $row['product_id'];
             $where = 'id = '.$row['product_id'];
            $out[$id]['product_name'] = myrowval('catalog_product', 'name',$where);
            $out[$id]['target'] = $row['target'];
            $out[$id]['achieved'] = $this->achieved_focus($row['start_date'],$row['end_date'],$product);
            
        }
       // pre($out);
        return $out;
    }
    
    /////////////////////////////////////////////////////////////////////////////////////
    public function achieved_focus($start,$end,$product)
    {
        global $dbc;
        $dealerId = $_SESSION[SESS.'data']['dealer_id'];
        $q= "SELECT SUM(rate*(quantity+scheme_qty)) as achieved FROM `user_primary_sales_order` upso INNER JOIN user_primary_sales_order_details upsod ON upsod.order_id = upso.order_id where is_claim=0 AND action=1 AND product_id='$product' AND ch_date >='$start' AND ch_date<='$end' AND dealer_id = $dealerId";
      // h1($q);
        $r = mysqli_query($dbc,$q);
        $row = mysqli_fetch_assoc($r);
        $unit = $row['achieved'];
        return $unit;
        //return 0;     
    }
    ///////////////////////////////////GET CHALLAN COUNT///////////////////////////////
     public function get_challan_count()
     {
       global $dbc;
        $out = array();
        $date = date("Y-m-d");
        $prev_date = date('Y-m-d', strtotime($date .' -1 day'));
        $state_id = $_SESSION[SESS . 'data']['state_id'];
        $dealer_id = $_SESSION[SESS . 'data']['dealer_id'];
       
        $q = "SELECT count(ch_no) as ch_count FROM 
            challan_order WHERE ch_dealer_id='$dealer_id' AND DATE_FORMAT(ch_date,'%Y-%m-%d')= '$prev_date'";
     //h1($q);
        $qprev = mysqli_query($dbc,$q);
       $row = mysqli_fetch_assoc($qprev);
         $out['prev'] = $row['ch_count'];
         $qt = "SELECT count(ch_no) as ch_count_to FROM 
            challan_order WHERE ch_dealer_id='$dealer_id' AND DATE_FORMAT(ch_date,'%Y-%m-%d')= '$date'";
     //h1($q);
        $qtoday = mysqli_query($dbc,$qt);
       $rowto = mysqli_fetch_assoc($qtoday);
         $out['today'] = $rowto['ch_count_to'];
        return $out;  
     }
     
     /////////////////////////////////////RETAILER////////////////////////////////////////
     ///////////////////////////////////GET CHALLAN COUNT///////////////////////////////
     public function total_retailer()
     {
       global $dbc;
        $out = array();
        $month = date('Y-m');
       // echo $month;
       // $state_id = $_SESSION[SESS . 'data']['state_id'];
        $dealer_id = $_SESSION[SESS . 'data']['dealer_id'];
       
        $q = "SELECT count(id) as retailer_count FROM 
            retailer WHERE dealer_id='$dealer_id'";
       // h1($q);
       $qtotal = mysqli_query($dbc,$q);
       $row = mysqli_fetch_assoc($qtotal);
         $out['total_retailer'] = $row['retailer_count'];
         
         $qm = "SELECT count(id) as retailer_count_m FROM 
            retailer WHERE dealer_id='$dealer_id' AND DATE_FORMAT(created_on,'%Y-%m')='$month'";
       $qtotalm = mysqli_query($dbc,$qm);
       $rowm = mysqli_fetch_assoc($qtotalm);
         $out['month_ret'] = $rowm['retailer_count_m'];
       // pre($out);
        return $out;  
     }
       ///////////////////////////////////GET INVOICE COUNT///////////////////////////////
     public function pending_invoice()
     {
       global $dbc;
        $out = array();
        $month = date('Y-m');
       // echo $month;
       // $state_id = $_SESSION[SESS . 'data']['state_id'];
        $dealer_id = $_SESSION[SESS . 'data']['dealer_id'];
       
                 
         $q= "SELECT count(id) as count FROM `user_sales_order` where dealer_id ='$dealer_id'";
      //  h1($q);
        $r = mysqli_query($dbc,$q);
        $row = mysqli_fetch_assoc($r);
        $count = $row['count'];
      //  $out['this'] = number_format($unit, 2, '.', ',');
       // pre($out);
        return $count;  
     }
     
     ///////////////////////////////////GET Invoice AMOUNT COUNT///////////////////////////////
     public function invoice_amount()
     {
       global $dbc;
        $out = array();
        $month = date('Y-m');
       // echo $month;
       // $state_id = $_SESSION[SESS . 'data']['state_id'];
        $dealer_id = $_SESSION[SESS . 'data']['dealer_id'];
       
                 
         $q= "SELECT SUM(amount) as amount, SUM(remaining) as remaining FROM `challan_order` 
              where DATE_FORMAT(ch_date,'%Y-%m') ='$month' AND ch_dealer_id = '$dealer_id'";
        //h1($q);
        $r = mysqli_query($dbc,$q);
        $row = mysqli_fetch_assoc($r);
        $unit = $row['amount'];
        $unit1= $row['remaining'];
       // setlocale(LC_MONETARY, 'en_IN');
       // $out['amount'] = money_format('%!i', $unit);
        $out['amount'] = $unit;
        $out['remaining'] = $row['remaining'];
      //  $out['remaining'] = money_format('%!i', $unit1);
       // pre($out);
        return $out;  
     }
     ///////////////////////////////////GET RECEIVE AMOUNT COUNT///////////////////////////////
     public function receive_amount()
     {
       global $dbc;
        $out = array();
        $month = date('Y-m');
       // echo $month;
       // $state_id = $_SESSION[SESS . 'data']['state_id'];
        $dealer_id = $_SESSION[SESS . 'data']['dealer_id'];
       
                 
         $q= "SELECT SUM(rate*(quantity)) as amount FROM `user_primary_sales_order` 
             upso INNER JOIN user_primary_sales_order_details upsod ON upsod.order_id = upso.order_id 
             where DATE_FORMAT(ch_date,'%Y-%m') ='$month' AND dealer_id = '$dealer_id'";
      //  h1($q);
        $r = mysqli_query($dbc,$q);
        $row = mysqli_fetch_assoc($r);
        $unit = $row['amount'];
       // setlocale(LC_MONETARY, 'en_IN');
        //$out['this'] = money_format('%!i', $unit);
        $out['this'] =$unit;
       // pre($out);
        return $out;  
     }
     ///////////////////////////??FOR PAYMENT//////////////////////////////////////////
     public function get_challan_prev()
     {
       global $dbc;
        $out = array();
        $date = date("Y-m-d");
        $prev_date = date('Y-m-d', strtotime($date .' -1 day'));
        $state_id = $_SESSION[SESS . 'data']['state_id'];
        $dealer_id = $_SESSION[SESS . 'data']['dealer_id'];
        $filterstr = $this->oo_filter($filter, $records, $orderby);
        $q = "SELECT ch_no,amount,ch_retailer_id,challan_order.id as ch_id, DATE_FORMAT(dispatch_date, '" . MASKDATE . "') AS dispatch_date,payment_status
           ,DATE_FORMAT(ch_date, '%Y-%m-%d') AS ch_date FROM 
            challan_order inner join dealer_location_rate_list dlrl on dlrl.dealer_id=ch_dealer_id WHERE ch_dealer_id='$dealer_id' AND DATE_FORMAT(ch_date,'%Y-%m-%d')= '$prev_date'";
     //h1($q);
        list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = '');
        if (!$opt)
            return $out;
        $dealer_map = get_my_reference_array('dealer', 'id', 'name');
      
       $retailer_map = get_my_reference_array('retailer', 'id', 'name');
        while ($row = mysqli_fetch_assoc($rs)){
           // pre($row);
            $id = $row['ch_id'];
            $out[$id] = $row; // storing the item id
            $out[$id]['retailer_id'] = $row['ch_retailer_id'];
            $out[$id]['dealer_name'] = $dealer_map[$row['ch_dealer_id']];
            $out[$id]['retailer_name'] = $retailer_map[$row['ch_retailer_id']];
            $q = "SELECT challan_order_details.*,challan_order_details.product_id AS pid, catalog_product.name,catalog_product_rate_list.surcharge,catalog_product_rate_list.comunity_code FROM "
                    . "challan_order_details INNER JOIN catalog_product ON catalog_product.id = challan_order_details.product_id "
                    . " INNER JOIN catalog_2 c2 ON c2.id = catalog_product.catalog_id "
                    . " INNER JOIN catalog_product_rate_list ON challan_order_details.product_id = catalog_product_rate_list.catalog_product_id "
                    . " AND catalog_product_rate_list.stateId ='$state_id'  WHERE ch_id = $id ";
           //  h1($q);
            $out[$id]['challan_item'] = $this->get_my_reference_array_direct($q,'id');
            
        }// while($row = mysqli_fetch_assoc($rs)){ ends
      // pre($out);
        return $out;  
     }
///////////////////////////??END PAYMENT///////////////////////////////////////////////////
///////////////////////////??DISTRIBUTOR COMPLAINT/////////////////////////////////////////
     
     public function complaint_list()
	{
		global $dbc;
                $dealer_id = $_SESSION[SESS . 'data']['dealer_id'];
		$out = array();
               // h1($filter);
              //  $filterstr=$this->oo_filter($filter, $records, $orderby);
                //$mtype = $_SESSION[SESS.'constant']['retailer_level'];
		$q = "SELECT * FROM `complaint` where dealer_retailer_id='$dealer_id'";
                //h1($q);
                $rs = mysqli_query($dbc,$q);            
		while($row = mysqli_fetch_assoc($rs)){
			$id = $row['order_id'];
			$out[$id] = $row; 
                        $out[$id]['user_name'] = $this->get_username($row['user_id']);
                        $out[$id]['type'] = $this->get_type($row['complaint_type']);
                        $cid = $row['complaint_id'];
                        $out[$id]['msg'] = $this->get_my_reference_array_direct("SELECT *, date as cdate FROM `complaint_history` WHERE complaint_history.complaint_id = $cid", 'id');  
                   
		}// while($row = mysqli_fetch_assoc($rs)){ ends
            // pre($out);
		return $out;	
	}
         public function complaint_history_list($compid)
	{
		global $dbc;
                $dealer_id = $_SESSION[SESS . 'data']['dealer_id'];
		$out = array();
               // h1($filter);
              //  $filterstr=$this->oo_filter($filter, $records, $orderby);
                //$mtype = $_SESSION[SESS.'constant']['retailer_level'];
		$q = "SELECT * FROM `complaint` where dealer_retailer_id='$dealer_id'";
                //h1($q);
                $rs = mysqli_query($dbc,$q);            
		while($row = mysqli_fetch_assoc($rs)){
			$id = $row['order_id'];
			$out[$id] = $row; 
                        $out[$id]['user_name'] = $this->get_username($row['user_id']);
                        $out[$id]['type'] = $this->get_type($row['complaint_type']);
                        $cid = $row['complaint_id'];
                        $out[$id]['msg'] = $this->get_my_reference_array_direct("SELECT *, date as cdate FROM `complaint_history` WHERE complaint_history.complaint_id = $cid", 'id');  
                   
		}// while($row = mysqli_fetch_assoc($rs)){ ends
            // pre($out);
		return $out;	
	}
      public function get_type($id)
	{
		global $dbc;
		$out = NULL;
		$q = "SELECT * FROM `complaint_type` WHERE id = $id";
		list($opt,$rs)= run_query($dbc,$q,$mode='single',$msg='');
                if(!$opt) return $out;
		return $rs['name'];	
	}
          public function get_username($id)
	{
		global $dbc;
		$out = NULL;
		$q = "SELECT CONCAT_WS(' ',first_name,middle_name,last_name) AS uname FROM person WHERE id = $id";
		list($opt,$rs)= run_query($dbc,$q,$mode='single',$msg='');
                if(!$opt) return $out;
		return $rs['uname'];	
	}
////////////////////////////END DISTRIBUTOR COMPLAINT///////////////////////////////////////
    public function get_profile_dtl_data() {
        $d1 = $_POST;        
        $d1['sesId'] = $_SESSION[SESS . 'sess']['sesId'];
        $d1['myreason'] = 'Please fill all the required information';
        $d1['what'] = 'Profile Details'; //whether to do history log or not
        return array(true, $d1);
    }
    
    public function profile_dtl_edit($id) {
       // echo "hrtyhrty"; exit;
        global $dbc;
        $out = array('status' => 'false', 'myreason' => '');
        list($status, $d1) = $this->get_profile_dtl_data();
        if (!$status)
            return array('staus' => false, 'myreason' => $d1['myreason']);       
        $receive_date = !empty($d1['receive_date']) ? get_mysql_date($d1['receive_date']) : '';
        $id = $id;
        $orderno = $d1['order_no'];
        
         $profile_pic = $_FILES['image']['name'];
        //Start the transaction
//echo $profile_pic;
        mysqli_query($dbc, "START TRANSACTION");
        $q = "UPDATE dealer_person_login SET person_name = '$d1[full_name]', uname = '$d1[user_name]' ,email = '$d1[email_id]',phone = '$d1[phone]' WHERE dealer_id = '$id'";
        //h1($q); exit;
        $r = mysqli_query($dbc, $q);
     
         if ($r) {
            $success1 = move_uploaded_file($_FILES['image']['tmp_name'], $_SERVER['DOCUMENT_ROOT']."/msell-dsgroup-dms/client/myuploads/profile_images/".$profile_pic);
            if ($success1) {
                $q1 = "UPDATE dealer_person_login SET profile_pic='$profile_pic' WHERE dealer_id = '$id'";
                $r1 = mysqli_query($dbc, $q1);
               $img1 = 'true';
            }

         }
        if (!$r) {
            mysqli_rollback($dbc);
            return array('status' => false, 'myreason' => 'Dealer person login table error');
        }
        
        
        mysqli_commit($dbc);     
       ?>
       <script>
        parent.location.reload();
       </script>

<?php
       // return array('status' => true, 'myreason' => $d1['what'] . ' successfully Updated');
        
    }
    
    public function get_retailer_reach_list($filter = '', $records = '', $orderby = '',$month) {
global $dbc;
$out = array();
$state_id = $_SESSION[SESS . 'data']['state_id'];
$dealer_id = $_SESSION[SESS . 'data']['dealer_id'];
$pmonth = Date("Y-m", strtotime($month . " last month")); 
$filterstr = $this->oo_filter($filter, $records, $orderby);
$q = "SELECT DISTINCT(uso.retailer_id) AS rid FROM user_sales_order AS uso WHERE DATE_FORMAT(uso.date,'%Y-%m')='$month' AND uso.dealer_id='$dealer_id'";
//h1($q);
list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = '');
if (!$opt)
return $out;
while ($row = mysqli_fetch_assoc($rs)) {
$id = $row['rid'];
$out[$id] = $row;
$out[$id]['rname'] = myrowval($table = "retailer", $col = "name", $where = "id = '$row[rid]'");// storing the item id
$holder= $this->get_calls_details($row[rid],$month,$dealer_id);
$out[$id]['total_call'] = $holder[0];
$out[$id]['productive'] = $holder[1];
$out[$id]['non-productive'] = $holder[2];
// pre($out); 

}
//pre($out);
return $out;
}


public function get_pre_retailer_reach_list($filter = '', $records = '', $orderby = '',$month) {
global $dbc;
$out = array();
$state_id = $_SESSION[SESS . 'data']['state_id'];
$dealer_id = $_SESSION[SESS . 'data']['dealer_id'];
$pmonth = Date("Y-m", strtotime($month . " last month")); 
$filterstr = $this->oo_filter($filter, $records, $orderby);
$q = "SELECT uso.retailer_id AS rid,retailer.name AS retname FROM user_sales_order AS uso INNER JOIN retailer ON retailer.id=uso.retailer_id WHERE DATE_FORMAT(uso.date,'%Y-%m')='$pmonth' AND uso.dealer_id='$dealer_id'";
//h1($q);
list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = '');
if (!$opt)
return $out;
while ($row = mysqli_fetch_assoc($rs)) {
$where = 'id = '.$row['rid'];
$id = $row['rid'];
$out[$id] = $row; // storing the item id
$holder= $this->get_calls_details($row[rid],$pmonth,$dealer_id);
$out[$id]['total_call'] = $holder[0];
$out[$id]['productive_pre'] = $holder[1];
$out[$id]['non-productive_pre'] = $holder[2];


}
// pre($out); 
return $out;
}


public function get_calls_details($id,$date,$dealer_id) {
global $dbc;
$out = NULL;

$q = "SELECT call_status FROM user_sales_order WHERE retailer_id = '$id' AND DATE_FORMAT(date,'%Y-%m')='$date' AND dealer_id=$dealer_id";
// h1($q);
list($opt, $rs) = run_query($dbc, $q, 'multi');
if (!$opt)
return $out;
$total_call = 0;
$productive = 0;
$non_productive = 0;
$total_Sale = 0;

while ($rows = mysqli_fetch_array($rs)) {
if ($rows['call_status'] == '1')
$productive++;
elseif ($rows['call_status'] == '0' || $rows['call_status'] == '') {
$non_productive++;
}
$total_call++;
}
$temp = array();

$temp[0] = $total_call;
$temp[1] = $productive;
$temp[2] = $non_productive;
// pre($temp);
return $temp;
}
public function get_total_bill_list($filter = '', $records = '', $orderby = '',$month) {
global $dbc;
$out = array();
$state_id = $_SESSION[SESS . 'data']['state_id'];
$dealer_id = $_SESSION[SESS . 'data']['dealer_id'];
$pmonth = Date("Y-m", strtotime($month . " last month")); 
$filterstr = $this->oo_filter($filter, $records, $orderby);
$q = "SELECT id,ch_no,amount FROM challan_order WHERE DATE_FORMAT(ch_date,'%Y-%m')='$month' AND ch_dealer_id='$dealer_id' ";
//h1($q);
list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = '');
if (!$opt)
return $out;
while ($row = mysqli_fetch_assoc($rs)) {
$id = $row['id'];
$out[$id] = $row;
//$out[$id]['rname'] = myrowval($table = "retailer", $col = "name", $where = "id = '$row[rid]'");// storing the item id

// pre($out); 

}
//pre($out);
return $out;
}
public function get_pre_total_bill_list($filter = '', $records = '', $orderby = '',$month) {
global $dbc;
$out = array();
$state_id = $_SESSION[SESS . 'data']['state_id'];
$dealer_id = $_SESSION[SESS . 'data']['dealer_id'];
$pmonth = Date("Y-m", strtotime($month . " last month")); 
$filterstr = $this->oo_filter($filter, $records, $orderby);
$q = "SELECT id,ch_no,amount FROM challan_order WHERE DATE_FORMAT(ch_date,'%Y-%m')='$pmonth' AND ch_dealer_id='$dealer_id' ";
//h1($q);
list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = '');
if (!$opt)
return $out;
while ($row = mysqli_fetch_assoc($rs)) {

$id = $row['id'];
$out[$id] = $row; // storing the item id


}
//pre($out); 
return $out;
}
public function get_total_recieve_list($filter = '', $records = '', $orderby = '',$month) {
global $dbc;
$out = array();
$state_id = $_SESSION[SESS . 'data']['state_id'];
$dealer_id = $_SESSION[SESS . 'data']['dealer_id'];
$pmonth = Date("Y-m", strtotime($month . " last month")); 
$filterstr = $this->oo_filter($filter, $records, $orderby);
$q = "SELECT `user_primary_sales_order`.`id` AS id, `user_primary_sales_order`.`challan_no` AS ch_no,(`user_primary_sales_order_details`.`quantity`*`user_primary_sales_order_details`.`rate`) AS amount FROM `user_primary_sales_order`  INNER JOIN user_primary_sales_order_details ON `user_primary_sales_order_details`.`order_id`=  `user_primary_sales_order`.`order_id`  WHERE DATE_FORMAT(ch_date,'%Y-%m')='$month' AND dealer_id='$dealer_id' GROUP BY user_primary_sales_order.order_id";
//h1($q);
list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = '');
if (!$opt)
return $out;
while ($row = mysqli_fetch_assoc($rs)) {
$id = $row['id'];
$out[$id] = $row;
//$out[$id]['rname'] = myrowval($table = "retailer", $col = "name", $where = "id = '$row[rid]'");// storing the item id

// pre($out); 

}
//pre($out);
return $out;
}
public function get_pre_total_recieve_list($filter = '', $records = '', $orderby = '',$month) {
global $dbc;
$out = array();
$state_id = $_SESSION[SESS . 'data']['state_id'];
$dealer_id = $_SESSION[SESS . 'data']['dealer_id'];
$pmonth = Date("Y-m", strtotime($month . " last month")); 
$filterstr = $this->oo_filter($filter, $records, $orderby);
$q = "SELECT `user_primary_sales_order`.`id` AS id, `user_primary_sales_order`.`challan_no` AS ch_no,(`user_primary_sales_order_details`.`quantity`*`user_primary_sales_order_details`.`rate`) AS amount FROM `user_primary_sales_order`  INNER JOIN user_primary_sales_order_details ON `user_primary_sales_order_details`.`order_id`=  `user_primary_sales_order`.`order_id`  WHERE DATE_FORMAT(ch_date,'%Y-%m')='$pmonth' AND dealer_id='$dealer_id' GROUP BY user_primary_sales_order.order_id";
//h1($q);
list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = '');
if (!$opt)
return $out;
while ($row = mysqli_fetch_assoc($rs)) {

$id = $row['id'];
$out[$id] = $row; // storing the item id


}
//pre($out); 
return $out;
}
public function get_pre_payment_collection_list($filter = '', $records = '', $orderby = '',$month) {
global $dbc;
$out = array();
$state_id = $_SESSION[SESS . 'data']['state_id'];
$dealer_id = $_SESSION[SESS . 'data']['dealer_id'];
$pmonth = Date("Y-m", strtotime($month . " last month")); 
$filterstr = $this->oo_filter($filter, $records, $orderby);
$q = "SELECT pc.retailer_id AS rid,retailer.name AS retname,pc.total_amount AS amount,pc.pay_mode AS mode,DATE_FORMAT(pc.pay_date_time,'%Y-%m-%d') AS date FROM payment_collection AS pc INNER JOIN retailer ON retailer.id=pc.retailer_id WHERE DATE_FORMAT(pc.pay_date_time,'%Y-%m')='$pmonth' AND pc.dealer_id='$dealer_id'";
//h1($q);
list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = '');
if (!$opt)
return $out;
while ($row = mysqli_fetch_assoc($rs)) {
$where = 'id = '.$row['rid'];

$id = $row['rid'];
$out[$id] = $row; 
$out[$id]['rname'] = myrowval($table = "retailer", $col = "name", $where = "id = '$row[rid]'");// storing the item id



}
// pre($out); 
return $out;
}
public function get_payment_collection_list($filter = '', $records = '', $orderby = '',$month) {
global $dbc;
$out = array();
$state_id = $_SESSION[SESS . 'data']['state_id'];
$dealer_id = $_SESSION[SESS . 'data']['dealer_id'];
$pmonth = Date("Y-m", strtotime($month . " last month")); 
$filterstr = $this->oo_filter($filter, $records, $orderby);
$q = "SELECT pc.retailer_id AS rid,retailer.name AS retname,pc.total_amount AS amount,pc.pay_mode AS mode,DATE_FORMAT(pc.pay_date_time,'%Y-%m-%d') AS date FROM payment_collection AS pc INNER JOIN retailer ON retailer.id=pc.retailer_id WHERE DATE_FORMAT(pc.pay_date_time,'%Y-%m')='$month' AND pc.dealer_id='$dealer_id'";
//h1($q);
list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = '');
if (!$opt)
return $out;
while ($row = mysqli_fetch_assoc($rs)) {
$id = $row['rid'];
$out[$id] = $row;
$out[$id]['rname'] = myrowval($table = "retailer", $col = "name", $where = "id = '$row[rid]'");// storing the item id

// pre($out); 

}
//pre($out);
return $out;
}


}
