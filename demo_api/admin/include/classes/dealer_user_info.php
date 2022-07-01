<?php
class dealer_user_info extends myfilter {

    public $id = NULL;
    public $id_detail = NULL;

    public function __construct($id = NULL) {
        parent::__construct();
        $this->id = $id;
    }
   
################################################################################

public function get_dealer_user_info_list($filter = '', $records = '', $orderby = '') {
    global $dbc;
    $out = array();

    $filterstr = $this->oo_filter($filter, $records, $orderby);
   // echo $filterstr;die;
    $q = "SELECT  udrv.user_name ,udrv.mobile ,udrv.retailer_name ,udrv.beat_id ,udrv.beat_name ,udrv.role_name ,udrv.user_id,
    udrv.l2_name ,udrv.l3_name,udrv.dealer_name ,count(distinct retailer_id) as retalier_count , count(distinct beat_id) as beat_count FROM `user_dealer_retailer_view` as udrv  inner join location_view as lv on lv.l5_id = udrv.beat_id
     inner join dealer on dealer.id =udrv.dealer_id   $filterstr group by  user_id , dealer_id 
  ";
   
 // h1($q);
    list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = '');
    if (!$opt)
        return $out;
   // $travelling_map = get_my_reference_array('_travelling_mode', 'id', 'mode');
    //$person_map = get_my_reference_array('person', 'id', 'name');
    while ($row = mysqli_fetch_assoc($rs)) {
      //  $id = $row['user_id'];
        $out[] = $row; // storing the item id
       // $out[$id]['travelling_mode'] = $travelling_map[$row['travelling_mode_id']];
      // $out[$id]['pname'] = $this->get_username($row['person_id']);
        //$out[$id]['working_status'] = $working_status_map[$row['working_id']];
    }// while($row = mysqli_fetch_assoc($rs)){ ends
 //  pre($out);
        return $out;
}


#########################################

    public function get_sale_month_report_details($filterstr, $start,$end) {
        //echo $filterstr;die;
        global $dbc;
        $out = array();
        $q = "SELECT emp_code,person.role_id as role,CONCAT_WS(' ',person.first_name,person.middle_name,
            person.last_name)as user_name,person.id as p_id,person.person_id_senior as senior_id,statename
            FROM person INNER JOIN person_login ON person_login.person_id=person.id 
            AND person_login.person_status='1' INNER JOIN state ON state.stateid=person.state_id
            INNER JOIN _role ON _role.role_id= person .role_id  $filterstr  GROUP BY person.id ORDER BY
                statename ASC";
      // h1($q);die;
        list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = '');
        if (!$opt)
            return $out;

        while ($row = mysqli_fetch_assoc($rs)) {
            $holder = array();
            $id = $row['s_id'] . $row['p_id'];
            //$out[$id] = $row; // storing the item id
            $out[$id]['p_id'] = $row['p_id'];
            
            $out[$id]['state'] = $row['statename'];
            $out[$id]['emp_code'] = $row['emp_code'];
            $out[$id]['name'] = $row['user_name'];
            $out[$id]['role'] = myrowval($table = "_role", $col = "rolename", $where = "role_id = '$row[role]'"); 
            $out[$id]['seniorname'] = myrowval($table = "person", $col = "CONCAT_WS(' ', first_name,middle_name,last_name)", $where = "id = '$row[senior_id]'");         
            $out[$id]['days'] = $this->get_total_dates($month);
            $out[$id]['monthly_sale'] = $this->get_total_sale_month($row['p_id'], $start,$end);
            $out[$id]['monthly_total'] = $this->get_grand_total_sale_month($row['p_id'], $start,$end);
            $out[$id]['working_days'] = $this->get_working_days_month($row['p_id'],$start,$end);
            $out[$id]['month_tc'] = $this->get_working_month_tc($row['p_id'],$start,$end);
            $out[$id]['month_pc'] = $this->get_working_month_pc($row['p_id'],$start,$end);
            
            
           
        }
    // pre($out);
        return $out;
    }

   



    #################################
      public function get_grand_total_sale_month_summary($state_id, $start,$end) {
        global $dbc;
            
          $out = NULL;        
     
       $q="SELECT sum(rate*quantity) as amt FROM  `user_sales_order` INNER JOIN user_sales_order_details usod using (order_id) INNER JOIN location_view AS lv ON lv.l5_id=location_id WHERE date_format(`date`,'%Y%m%d') >= '$start' AND date_format(`date`,'%Y%m%d') <= '$end' AND l2_id='$state_id'";
      // h1($q);
        list($opt, $rs) = run_query($dbc, $q, $mode = 'single', $msg = '');
        if (!$opt)
            return $out;
       // pre($rs);
        return $rs['amt'];
    }
     public function get_working_month_tc_summary($state_id, $start,$end) {
        global $dbc;
            
          $out = NULL;        
     
       $q="SELECT count(DISTINCT `retailer_id`) as month_tc FROM  `user_sales_order` INNER JOIN user_sales_order_details usod using (order_id) INNER JOIN location_view AS lv ON lv.l5_id=location_id WHERE date_format(`date`,'%Y%m%d') >= '$start' AND date_format(`date`,'%Y%m%d') <= '$end' AND l2_id='$state_id'";
      // h1($q);
        list($opt, $rs) = run_query($dbc, $q, $mode = 'single', $msg = '');
        if (!$opt)
            return $out;
       // pre($rs);
        return $rs['month_tc'];
    }
    public function get_no_usage_month_summary($state_id, $start,$end,$role_id) {
        global $dbc;
            
          $out = NULL;        
     if(!empty($role_id)){
       $q="SELECT count(person.id) AS no_usage FROM person INNER JOIN person_login ON person_login.person_id=person.id WHERE id NOT IN(SELECT `user_id` FROM `user_sales_order` INNER JOIN location_view ON l5_id=location_id WHERE date_format(`date`,'%Y%m%d') >= '$start' AND date_format(`date`,'%Y%m%d') <= '$end' AND l2_id='$state_id' GROUP BY user_id) AND state_id='$state_id' AND person_status='1' AND person.role_id IN($role_id) ";
     } else{
      $q="SELECT count(person.id) AS no_usage FROM person INNER JOIN person_login ON person_login.person_id=person.id WHERE id NOT IN(SELECT `user_id` FROM `user_sales_order` INNER JOIN location_view ON l5_id=location_id WHERE date_format(`date`,'%Y%m%d') >= '$start' AND date_format(`date`,'%Y%m%d') <= '$end' AND l2_id='$state_id' GROUP BY user_id) AND state_id='$state_id' AND person_status='1' ";

     }
      // h1($q);
        list($opt, $rs) = run_query($dbc, $q, $mode = 'single', $msg = '');
        if (!$opt)
            return $out;
       // pre($rs);
        return $rs['no_usage'];

    }
    public function get_regular_usage_month_summary($state_id, $start,$end,$role_id) {
                global $dbc;
                $out = NULL; 
                $start1=date_create($start);                
                $start_date=date_format($start1,'Y-m-d');
                $end1=date_create($end);
                $end_date1=date_format($end1,'Y-m-d');
                $start_ts = strtotime($start_date);
                $end_ts = strtotime($end_date1);
                $diff = $end_ts - $start_ts;
                $datediff=round($diff / 86400);
               // h1($datediff);
                if(!empty($role_id)){
       $q="SELECT person.id,(SELECT count(DISTINCT `date`) as working_days FROM `user_sales_order` WHERE date_format(`date`,'%Y%m%d') >= '$start' AND date_format(`date`,'%Y%m%d') <= '$end' AND user_id=person.id) AS working_days FROM person INNER JOIN person_login ON person.id=person_login.person_id WHERE state_id='$state_id' AND person_status='1' AND person.role_id IN($role_id)";
     }
     else{
      $q="SELECT person.id,(SELECT count(DISTINCT `date`) as working_days FROM `user_sales_order` WHERE date_format(`date`,'%Y%m%d') >= '$start' AND date_format(`date`,'%Y%m%d') <= '$end' AND user_id=person.id) AS working_days FROM person INNER JOIN person_login ON person.id=person_login.person_id WHERE state_id='$state_id' AND person_status='1'";
     }
       //$q="SELECT count(person.id) AS no_usage FROM person WHERE id NOT IN(SELECT `user_id` FROM `user_sales_order` INNER JOIN user_sales_order_details usod using (order_id) WHERE date_format(`date`,'%Y%m%d') >= '$start' AND date_format(`date`,'%Y%m%d') <= '$end') AND state_id='$state_id' ";
       //h1($q);
       $rs= mysqli_query($dbc,$q);
       // list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = '');
      //  if (!$run)
           // return $out;
        $regular=0;
          while ($row = mysqli_fetch_assoc($rs)) {
              $count=$row[working_days];
              if($count>=($datediff-2)){
                  $regular=$regular+1;
              }
          }
        return $regular;
    }
    
     public function get_working_month_pc_summary($state_id, $start,$end) {
        global $dbc;
            
          $out = NULL;        
     
       $q="SELECT count(DISTINCT `retailer_id`) as month_pc FROM  `user_sales_order` INNER JOIN user_sales_order_details usod using (order_id) INNER JOIN location_view AS lv ON lv.l5_id=location_id WHERE date_format(`date`,'%Y%m%d') >= '$start' AND date_format(`date`,'%Y%m%d') <= '$end' AND call_status='1' AND l2_id='$state_id'";
      // h1($q);
        list($opt, $rs) = run_query($dbc, $q, $mode = 'single', $msg = '');
        if (!$opt)
            return $out;
       // pre($rs);
        return $rs['month_pc'];
    }
     public function get_total_sale_month($person, $start,$end) {
        global $dbc;  
        $data = array();   
       // ,count(call_status) as pc 
        $q = "SELECT sum(rate*quantity) as total_amt,date FROM  `user_sales_order` INNER JOIN user_sales_order_details usod using (order_id) WHERE `call_status`='1' AND date_format(`date`,'%Y%m%d') >= '$start' AND date_format(`date`,'%Y%m%d') <= '$end' AND user_id='$person' GROUP BY date";
       // h1($q);
        $q_res = mysqli_query($dbc, $q);
        while ($q_row = mysqli_fetch_array($q_res)) {
            //pre($q_row);
            $id = $q_row['date'];
            $data[$id] = $q_row['total_amt'];
           // $data[$id."pc"] = $q_row['pc'];
            $where1  = "`date`='$id' AND user_id='$person' AND Call_status='1'";
            $data[$id."pc"] = myrowval($table = 'user_sales_order',$col= 'count(DISTINCT retailer_id)', $where1);
            $where  = "`date`='$id' AND user_id='$person'";
            $data[$id."tc"] = myrowval($table = 'user_sales_order',$col= 'count(DISTINCT retailer_id)', $where);
        }
  
        return $data;
    }
    
    
    public function get_grand_total_sale_month($person, $start,$end) {
        global $dbc;
            
          $out = NULL;        
     
       $q="SELECT sum(rate*quantity) as amt FROM  `user_sales_order` INNER JOIN user_sales_order_details usod using (order_id) WHERE date_format(`date`,'%Y%m%d') >= '$start' AND date_format(`date`,'%Y%m%d') <= '$end' AND user_id='$person'";
      // h1($q);
        list($opt, $rs) = run_query($dbc, $q, $mode = 'single', $msg = '');
        if (!$opt)
            return $out;
       // pre($rs);
        return $rs['amt'];
    }

    public function get_working_days_month($person, $start,$end) {
        global $dbc;
            
          $out = NULL;        
     
       $q="SELECT count(DISTINCT `date`) as working_days FROM  `user_sales_order` INNER JOIN user_sales_order_details usod using (order_id) WHERE date_format(`date`,'%Y%m%d') >= '$start' AND date_format(`date`,'%Y%m%d') <= '$end' AND user_id='$person'";
      // h1($q);
        list($opt, $rs) = run_query($dbc, $q, $mode = 'single', $msg = '');
        if (!$opt)
            return $out;
       // pre($rs);
        return $rs['working_days'];
    }
   
    public function get_working_month_tc($person, $start,$end) {
        global $dbc;
            
          $out = NULL;        
     
       $q="SELECT DISTINCT  date, (`retailer_id`) as month_tc FROM  `user_sales_order` INNER JOIN user_sales_order_details usod using (order_id) WHERE date_format(`date`,'%Y%m%d') >= '$start' AND date_format(`date`,'%Y%m%d') <= '$end' AND user_id='$person' GROUP BY date,retailer_id";
        // h1($q);
        // die;
        list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = '');
        if (!$opt)
            return $out;

        $r1=array();
        while($r = mysqli_fetch_assoc($rs))
        {
           $r1[] = $r['retailer_id'];
        }

        // return $rs['month_tc'];
        return count($r1);
    }
    public function get_working_month_pc($person, $start,$end) {
        global $dbc;
            
          $out = NULL;        
     
       $q="SELECT DISTINCT  date, (`retailer_id`) as month_pc FROM  `user_sales_order` INNER JOIN user_sales_order_details usod using (order_id) WHERE date_format(`date`,'%Y%m%d') >= '$start' AND date_format(`date`,'%Y%m%d') <= '$end' AND call_status='1' AND user_id='$person'  GROUP BY date,retailer_id";
      // h1($q);
        list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = '');
        if (!$opt)
            return $out;

        $r1=array();
        while($r = mysqli_fetch_assoc($rs))
        {
           $r1[] = $r['retailer_id'];
         }
         return count($r1);
         // return $rs['month_pc'];
    }
     public function get_retailer_winback_45($filter = '', $records = '', $orderby = '') {
        global $dbc;
        $out = array();
      // pre($filter);
        $date1 = date("Y-m-d");
        $newDate = strtotime($date1);
        $date_45 = $newDate-(86400*45);
        $day45 = date('Y-m-d',$date_45);
      //  $day60 = date('Y-m-d',$date_60);
       
        $filterstr = $this->oo_filter($filter, $records, $orderby);
       // echo $filterstr;die;
        $q = "SELECT retailer.id as rid ,name FROM retailer WHERE retailer.$filter[0] AND retailer.id  NOT IN (SELECT retailer_id  FROM user_sales_order
            WHERE user_sales_order.$filter[0] AND date BETWEEN '$day45' AND '$date1')";
        // h1($q);
        list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = '');
        if (!$opt)
            return $out;
       // $travelling_map = get_my_reference_array('_travelling_mode', 'id', 'mode');
       
        $in = 0;
        while ($row = mysqli_fetch_assoc($rs)) {
           // $id = $row['id'];
            $out[$in] = $row; 
             $rid = $row['rid'];
            $out[$in]['date'] = $this->get_retailer_order($rid);
            $in++;
         }
        return $out;
    }
     public function get_retailer_winback_60($filter = '', $records = '', $orderby = '') {
        global $dbc;
        $out = array();
      // pre($filter);
        $date1 = date("Y-m-d");
        $newDate = strtotime($date1);
        $date_60 = $newDate-(86400*60);
        $day60 = date('Y-m-d',$date_60);
       
        $filterstr = $this->oo_filter($filter, $records, $orderby);
       // echo $filterstr;die;
        $q = "SELECT retailer.id as rid ,name FROM retailer WHERE retailer.$filter[0] AND retailer.id  NOT IN (SELECT retailer_id  FROM user_sales_order
            WHERE user_sales_order.$filter[0] AND date BETWEEN '$day60' AND '$date1')";
       //  h1($q);
        list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = '');
        if (!$opt)
            return $out;
       // $travelling_map = get_my_reference_array('_travelling_mode', 'id', 'mode');
       
       $in = 0;
        while ($row = mysqli_fetch_assoc($rs)) {
           // $id = $row['id'];
            $out[$in] = $row; 
             $rid = $row['rid'];
            $out[$in]['date'] = $this->get_retailer_order($rid);
            $in++;
         }
        return $out;
    }
    
     public function get_retailer_winback_list($filter = '', $records = '', $orderby = '') {
        global $dbc;
        $out = array();
      // pre($filter);
        $date1 = date("Y-m-d");
        $newDate = strtotime($date1);
        $date_15 = $newDate-(86400*15);
        $date_30 = $newDate-(86400*30);
        $date_45 = $newDate-(86400*45);
        $date_60 = $newDate-(86400*60);
        $day15 = date('Y-m-d',$date_15);
        $day30 = date('Y-m-d',$date_30);
        $day45 = date('Y-m-d',$date_45);
       $day60 = date('Y-m-d',$date_60);
       
        $filterstr = $this->oo_filter($filter, $records, $orderby);
       // echo $filterstr;die;
        $q = "SELECT retailer.id as rid ,name FROM retailer WHERE retailer.$filter[0] AND retailer.id  NOT IN (SELECT DISTINCT retailer_id  FROM user_sales_order
            WHERE user_sales_order.$filter[0] AND date BETWEEN '$day15' AND '$date1')";
       // h1($q);
        list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = '');
        if (!$opt)
            return $out;
       // $travelling_map = get_my_reference_array('_travelling_mode', 'id', 'mode');
       $in = 0;
        while ($row = mysqli_fetch_assoc($rs)) {
           // $id = $row['id'];
            $out[$in] = $row; 
            $rid = $row['rid'];
            $out[$in]['date'] = $this->get_retailer_order($rid);
            $in++;
         }
        // pre($out);
        return $out;
    }
    
     public function get_retailer_order($id) {
        global $dbc;
        $out = NULL;
        $q = "SELECT `date`  FROM user_sales_order WHERE retailer_id = '$id' AND call_status = '1' order by date desc limit 1";
     // h1($q);
       $qr = mysqli_query($dbc,$q);
       $row = mysqli_fetch_assoc($qr);
       if(empty($row))
           $row['date'] = "No Booking";
        return $row['date'];
    }
    ################################ expense report###############################################


public function get_user_expanse_list($filter = '', $records = '', $orderby = '') {
        global $dbc;
        $out = array();

        $filterstr = $this->oo_filter($filter, $records, $orderby);
       // echo $filterstr;die;
        $q = "SELECT user_expense_report.*,_role.rolename AS role,location_view.state_name AS state,location_view.state_id,DATE_FORMAT(submit_date,'%e/%b/%Y') AS fdated FROM user_expense_report INNER JOIN person ON person.id=user_expense_report.person_id INNER JOIN location_view ON person.state_id=location_view.state_id INNER JOIN _role ON _role.role_id=person.role_id  $filterstr";
      //h1($q);
        list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = '');
        if (!$opt)
            return $out;
        $travelling_map = get_my_reference_array('_travelling_mode', 'id', 'mode');
        //$person_map = get_my_reference_array('person', 'id', 'name');
        while ($row = mysqli_fetch_assoc($rs)) {
            $id = $row['id'];
            $out[$id] = $row; // storing the item id
            $out[$id]['travelling_mode'] = $travelling_map[$row['travelling_mode_id']];
            $out[$id]['pname'] = $this->get_username($row['person_id']);
            //$out[$id]['working_status'] = $working_status_map[$row['working_id']];
        }// while($row = mysqli_fetch_assoc($rs)){ ends
        return $out;
    }


################################ expense report###############################################
   
    //This function is used to fetch the price from hollow section
    public function get_sale_value($rateid, $qty, $rate) {
        global $dbc;
        $value = 0;
        switch ($rateid) {
            case 0: {
                    $value = $rate * $qty;
                    $value = $value == 0 ? '---' : $value;
                    break;
                }
        }
        return $value;
    }

    public function get_sale_order_se_data() {
        $d1 = $_POST;
        $d1['uid'] = $_SESSION[SESS . 'data']['id'];
        $d1['myreason'] = 'Please fill all the required information';
        $d1['what'] = 'Sale Order'; //whether to do history log or not
        return array(true, $d1);
    }

    public function sale_order_save() {

        global $dbc;
        $out = array('status' => 'false', 'myreason' => '');
        list($status, $d1) = $this->get_sale_order_se_data();
        if (!$status)
            return array('staus' => false, 'myreason' => $d1['myreason']);
        $upload_path = $this->get_document_type_list($filter = "id IN (2)", $records = '', $orderby = '');
        $sale_path = $upload_path[2]['documents_location'];
        $sale_path = MYUPLOADS . $sale_path;
        $browse_file = $_FILES['image_name']['name'];
        if (!empty($browse_file)) {
            list($uploadstat, $filename) = fileupload('image_name', $sale_path, $allowtype = array('image/jpeg', 'image/png', 'image/gif'), $maxsize = 52428800, $mandatory = true);
            if ($uploadstat) {
                resizeimage($filename, $sale_path, $newwidth = 400, $thumbnailwidth = 200, MSYM, $thumbnail = true);
            }
        } else
            $filename = '';

        $id = $orderno = $d1['uid'] . date('Ymdhis');
        //Start the transaction
        mysqli_query($dbc, "START TRANSACTION");
        $q = "INSERT INTO `user_sales_order` 
                        (`id`,`order_id`, `user_id`, `retailer_id`,`dealer_id`, `location_id`, `call_status`,`lat_lng`,`mccmnclatcellid`,`date`, `time`,`image_name`,`remarks`, `company_id`) 
                    VALUES ('$id','$orderno', '$d1[uid]', '$d1[retailer_id]','$d1[dealer_id]', '$d1[location_id]', '$d1[call_status]',0,0,NOW(), NOW(),'$filename','$d1[remarks]', '{$_SESSION[SESS . 'data']['company_id']}')";

        $r = mysqli_query($dbc, $q);
        if (!$r) {
            mysqli_rollback($dbc);
            return array('status' => false, 'myreason' => 'Sales Table error');
        }
        $rId = $id;
        $i = 0;
        $total_sale_value = array();
        if (!empty($d1['product'])) {
            foreach ($d1['product'] as $key => $value) {
                $prod = $d1['product'][$key];
                $rate = $d1['base_price'][$key];
                $qty = $d1['quantity'][$key];
                $schqty = $d1['scheme'][$key];
                $total1 = $d1['prodvalue'][$key];
                $total_sale_value[] = $total1;
                $uncode = $key + 1;
                //To save the value of the other columns as some columns are affected by po
                $str[] = "('$uncode', '$orderno', '$prod', '$rate', '$qty', '$schqty')";
            }
            $str = implode(', ', $str);
            $total_sum = array_sum($total_sale_value);

            $q = "INSERT INTO `user_sales_order_details` (`id`,`order_id`,`product_id`,`rate`,`quantity`,`scheme_qty`) "
                    . "VALUES $str";

            $r = mysqli_query($dbc, $q);

            if (!$r)
                return array('status' => false, 'myreason' => 'Sales could not be saved, some error occurred');
            if (!empty($d1['gift_id'])) {
                $str1 = array();
                foreach ($d1['gift_id'] as $key => $value) {
                    $gift = $d1['gift_id'][$key];
                    $uncode = $key + 1;
                    $gift_qty = $d1['gift_qty'][$key];
                    $str1[] = "('$uncode','$orderno','$gift','$gift_qty')";
                }
                $str1 = implode(', ', $str1);
                $q = "INSERT INTO `user_retailer_gift_details` (`id`,`order_id`,`gift_id`,`quantity`) "
                        . "VALUES $str1";
                $r = mysqli_query($dbc, $q);
                if (!$r)
                    return array('status' => false, 'myreason' => 'Gift  Table error');
            }
            $q11 = "UPDATE user_sales_order SET total_sale_value = '$total_sum' WHERE order_id = '$orderno' ";
            $r111 = mysqli_query($dbc, $q11);

            mysqli_commit($dbc);
        }
        //Logging the history
        //history_log($dbc, 'Add', 'stock received <b>'.$d1['billno'].'</b> with With RefCode : '.$rId, $d1['what']);
        //Final success 
        return array('status' => true, 'myreason' => $d1['what'] . ' successfully Saved', 'rId' => $rId);
    }

//$opt1 end here

    public function sale_order_edit($id) {
        global $dbc;
        $out = array('status' => 'false', 'myreason' => '');
        list($status, $d1) = $this->get_sale_order_se_data();
        if (!$status)
            return array('staus' => false, 'myreason' => $d1['myreason']);
        $orderno = $this->next_order_num();
        $total_sale_value = $this->get_sale_value($d1['catalog_1_id'], $d1['metric_ton']);
        //Start the transaction
        mysqli_query($dbc, "START TRANSACTION");
        // query to update
        $q = "UPDATE user_sales_order SET user_id='$d1[uid]',retailer_id = '$d1[retailer_id]',order_id='$id',call_status = '$d1[call_status]',total_sale_value = '$d1[total_sale_value]',date = NOW(),time = NOW(), company_id = '{$_SESSION[SESS . 'data']['company_id']}', remarks= '$d1[remarks]' WHERE id = '$id'";

        $r = mysqli_query($dbc, $q);
        if (!$r) {
            mysqli_rollback($dbc);
            return array('status' => false, 'myreason' => 'Sales Could not be updated, some error occurred');
        }
        $rId = $id;

        $total_sale_value = array();
        if (!empty($d1['product'])) {
            foreach ($d1['product'] as $key => $value) {
                $prod = $d1['product'][$key];
                $rate = $d1['base_price'][$key];
                $qty = $d1['quantity'][$key];
                $schqty = $d1['scheme'][$key];
                $total1 = $d1['prodvalue'][$key];
                $total_sale_value[] = $total1;
                $uncode = $key + 1;
                //To save the value of the other columns as some columns are affected by po
                $str[] = "('$uncode', '$id', '$prod', '$rate', '$qty', '$schqty')";
            }
            $str = implode(', ', $str);
            $total_sum = array_sum($total_sale_value);
            $q = "DELETE FROM user_sales_order_details WHERE order_id = '$id'";
            $r = mysqli_query($dbc, $q);
            if (!$r) {
                mysqli_rollback($dbc);
                return array('status' => false, 'myreason' => 'Sales Could not be updated some error occurred.');
            }
            $q = "INSERT INTO `user_sales_order_details` (`id`,`order_id`,`product_id`,`rate`,`quantity`,`scheme_qty`) "
                    . "VALUES $str";

            $r = mysqli_query($dbc, $q);

            if (!$r)
                return array('status' => false, 'myreason' => 'Sales could not be saved, some error occurred');
            if (!empty($d1['gift_id'])) {
                $str1 = array();
                foreach ($d1['gift_id'] as $key => $value) {
                    $gift = $d1['gift_id'][$key];
                    $uncode = $key + 1;
                    $gift_qty = $d1['gift_qty'][$key];
                    $str1[] = "('$uncode','$id','$gift','$gift_qty')";
                }
                $str1 = implode(', ', $str1);
                $q = "DELETE FROM user_retailer_gift_details WHERE order_id = '$id'";
                $r = mysqli_query($dbc, $q);
                if (!$r) {
                    mysqli_rollback($dbc);
                    return array('status' => false, 'myreason' => 'Sales Could not be updated some error occurred.');
                }
                $q = "INSERT INTO `user_retailer_gift_details` (`id`,`order_id`,`gift_id`,`quantity`) "
                        . "VALUES $str1";
                $r = mysqli_query($dbc, $q);
                if (!$r)
                    return array('status' => false, 'myreason' => 'Sales order could not be updated some error occurred.');
            }
            $q11 = "UPDATE user_sales_order SET total_sale_value = '$total_sum' WHERE order_id = '$orderno' ";
            $r111 = mysqli_query($dbc, $q11);

            mysqli_commit($dbc);
        }

        //Saving the user modification history
        //$hid = history_log($dbc, 'Edit', 'stock received <strong>'.$d1['billno'].'</strong> With RefCode :'.$id);
        //$this->save_log($hid, $modifieddata, $d1['what']);
        return array('status' => true, 'myreason' => $d1['what'] . ' successfully Updated');
    }

    public function get_sale_order_list($filter = '', $records = '', $orderby = '') {
        global $dbc;
        $out = array();
        $filterstr = $this->oo_filter($filter, $records, $orderby);
        $mtype = $_SESSION[SESS . 'constant']['retailer_level'];
        /*$q = "SELECT *,lv.l2_name as state,lv.l5_name as beat,DATE_FORMAT(date,'%d/%m/%Y') AS sale_date,DATE_FORMAT(time,'%d/%m/%Y') AS sale_time,DATE_FORMAT(date,'%d/%b/%Y') AS fdated FROM user_sales_order uso"
                . " INNER JOIN location_view lv ON uso.location_id=lv.l5_id "
                . " $filterstr";*/

        $q = "SELECT uso.call_status,uso.order_id,lv.l2_name as state,uso.user_id,lv.l5_name as beat,DATE_FORMAT(date,'%d/%m/%Y') AS sale_date,DATE_FORMAT(time,'%d/%m/%Y') AS sale_time,DATE_FORMAT(date,'%d/%b/%Y') AS fdated, d.name , r.name as firm_name,uso.track_address,uso.image_name FROM user_sales_order uso"
                . " INNER JOIN location_view lv ON uso.location_id=lv.l5_id "
                . " INNER JOIN dealer d ON uso.dealer_id=d.id "
                . " INNER JOIN retailer r ON uso.retailer_id=r.id "
                . " $filterstr";
       //h1($q);exit;
        list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = '');
        if (!$opt)
            return $out;
        $retailer_map = get_my_reference_array('retailer', 'id', 'name');
       /* $dealer_map = get_my_reference_array('dealer', 'id', 'name');
        $brand_map = get_my_reference_array('catalog_1', 'id', 'name');*/
        while ($row = mysqli_fetch_assoc($rs)) {
            // pre($row);
            $id = $row['order_id'];
            $out[$id] = $row; // storing the item id
            $out[$id]['name'] = $row['name'];
            $out[$id]['firm_name'] = $row['firm_name'];
            $out[$id]['person_name'] = $this->get_username($row['user_id']);

            /*$out[$id]['order_item'] = $this->get_my_reference_array_direct("SELECT usod.id,cp.name,usod.rate as base_price,usod.quantity,usod.scheme_qty,"
                    . "usod.product_id FROM user_sales_order_details usod INNER JOIN user_sales_order uso ON usod.order_id = uso.order_id "
                    . "INNER JOIN catalog_product cp ON usod.product_id=cp.id 
                    INNER JOIN catalog_2 c2 ON c2.id = cp.catalog_id  "
                    . "INNER JOIN person ON person.id = uso.user_id 
                    INNER JOIN state ON person.state_id = state.stateid "
                    . "INNER JOIN product_rate_list cprl ON usod.product_id = cprl.product_id AND state.stateid = cprl.state_id  "
                    . "WHERE user_id ='$row[user_id]' AND usod.order_id = $row[order_id]", 'id');*/
            $q1="SELECT usod.id,cp.name,usod.rate as base_price,usod.quantity,usod.scheme_qty,"
                    . "usod.product_id FROM user_sales_order_details usod INNER JOIN user_sales_order uso ON usod.order_id = uso.order_id "
                    . "INNER JOIN catalog_product cp ON usod.product_id=cp.id "                    
                    . "WHERE user_id ='$row[user_id]' AND usod.order_id = $row[order_id]";
           //h1($q1);exit;

            $out[$id]['order_item'] = $this->get_my_reference_array_direct($q1, 'id');


            // $out[$id]['gift_item'] = $this->get_my_reference_array_direct("SELECT * FROM user_retailer_gift_details INNER JOIN _retailer_mkt_gift ON _retailer_mkt_gift.id = user_retailer_gift_details.gift_id WHERE order_id = '$row[order_id]'", 'id');
            // $out[$id]['merch_item'] = $this->get_my_reference_array_direct("SELECT id,qty,`merchandise_name`,image from `merchandise` WHERE  `merchandise`.`order_id`='$id'", 'id');
        }// while($row = mysqli_fetch_assoc($rs)){ ends
      // pre($out);
        return $out;
    }
    ###########################user_monthlysale######################################
    public function get_user_monthlysale_ord_list($filter = '', $records = '', $orderby = '') {
        global $dbc;
        $out = array();
        $filterstr = $this->oo_filter($filter, $records, $orderby);
        $mtype = $_SESSION[SESS . 'constant']['retailer_level'];
        $end = get_mysql_date($_POST['to_date'],'/',$time = false, $mysqlsearch = true);
        $start = get_mysql_date($_POST['from_date'],'/',$time = false, $mysqlsearch = true);
        $start_date = date('Y-m-d',strtotime($start)); // date convert in Y-m-d format
        $end_date =  date('Y-m-d',strtotime($end));

        $q = "SELECT uso.* , lv.l5_name as beat ,lv.l4_name as town , lv.l3_name as city ,lv.l2_name as state ,
        CONCAT_WS(' ',name, other_numbers) AS dealer_name FROM user_sales_order as uso "
        ." INNER JOIN person ON person.id=uso.user_id  "
        ." INNER JOIN location_view lv ON lv.l5_id=uso.location_id " 
        ." INNER JOIN dealer ON dealer.id=uso.dealer_id"        
        . " $filterstr";
   h1($q);//exit;
        list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = '');
        if (!$opt)
            return $out;


        while ($row = mysqli_fetch_assoc($rs)) {
            // pre($row);
            $id = $row['order_id'];
            $out[$id] = $row; // storing the item id
            $out[$id]['name'] = $row['name'];
            $out[$id]['working_with'] = myrowval($table = "_working_status", $col = "name", $where = "id = '$row[working_status_id]'");
            $out[$id]['firm_name'] = $row['firm_name'];
            $out[$id]['person_name'] = $this->get_username($row['person_id']);
          //  $out[$id]['productive'] = $this->get_app_productive_status($row['person_id'],$start_date,$end_date);
          
        //     $q1="SELECT usod.id,cp.name,usod.rate as base_price,usod.quantity,usod.scheme_qty,"
        //             . "usod.product_id FROM user_sales_order_details usod INNER JOIN user_sales_order uso ON usod.order_id = uso.order_id "
        //             . "INNER JOIN catalog_product cp ON usod.product_id=cp.id "                    
        //             . "WHERE user_id ='$row[user_id]' AND usod.order_id = $row[order_id]";
        //    //h1($q1);exit;

        //     $out[$id]['order_item'] = $this->get_my_reference_array_direct($q1, 'id');


            // $out[$id]['gift_item'] = $this->get_my_reference_array_direct("SELECT * FROM user_retailer_gift_details INNER JOIN _retailer_mkt_gift ON _retailer_mkt_gift.id = user_retailer_gift_details.gift_id WHERE order_id = '$row[order_id]'", 'id');
            // $out[$id]['merch_item'] = $this->get_my_reference_array_direct("SELECT id,qty,`merchandise_name`,image from `merchandise` WHERE  `merchandise`.`order_id`='$id'", 'id');
        }// while($row = mysqli_fetch_assoc($rs)){ ends
      // pre($out);
        return $out;
    }
    
   ##############################################

   public function get_app_productive_status($user,$start_date){
    global $dbc;
    $tot=array();
    //$row=array();
    $q="Select mtp.* , lv.l5_name as beat ,lv.l4_name as town , lv.l3_name as city ,
    lv.l2_name as state ,
    CONCAT_WS(' ',name, other_numbers) AS dealer_name FROM  monthly_tour_program as mtp 
    INNER JOIN location_view lv ON lv.l5_id=mtp.locations 
    INNER JOIN dealer ON dealer.id=mtp.dealer_id 
     WHERE DATE_FORMAT(`working_date`,'%d-%m-%Y')='".$start_date."' AND person_id=".$user." LIMIT 1";
  //  h1($q);
    $res=mysqli_query($dbc,$q);
    $row=mysqli_fetch_assoc($res);
     $id = $row['id'];
     $tot[]=$row;
    //$working_status_id =$row['working_status_id'];
    // h1($working_status_id);
   $row['working_with'] = myrowval($table = "_working_status", $col = "name", $where = "id = '$row[working_status_id]'");    
   // pre($tot);
    return $tot;
}
  
 ###############################################
 public function get_dealer_deatils($user,$start_date){
    $start_date = date('Y-m-d',strtotime($start_date));
    global $dbc;
    $out=array();
    //$row=array();
    $q="Select 
    CONCAT_WS(' ',name, other_numbers) AS dealer_name FROM user_sales_order as uso
    INNER JOIN dealer ON dealer.id=uso.dealer_id 
     WHERE DATE_FORMAT(`date`,'%Y-%m-%d')='".$start_date."' 
     AND user_id=".$user." group by dealer_id"; 
   // h1($q);
    $res1=mysqli_query($dbc,$q);
   while($row=mysqli_fetch_array($res1)) 
    {  $id = $row['id'];
    // print_r($row);
     $out=$row;
 }
     $dealer = implode(',',$out);

    return  $dealer;
} 
  
  ###############################################
 public function get_beat_deatils($user,$start_date){
    $start_date = date('Y-m-d',strtotime($start_date));
    global $dbc;
    $out=array();
    //$row=array();
    $q="Select 
    location_5.name  FROM user_sales_order as uso
    INNER JOIN location_5 ON location_5.id=uso.location_id 
     WHERE DATE_FORMAT(`date`,'%Y-%m-%d')='".$start_date."' 
     AND user_id=".$user." group by location_id"; 
   // h1($q);
    $res1=mysqli_query($dbc,$q);
   while($row=mysqli_fetch_array($res1)) 
    {  $id = $row['id'];
    // print_r($row);
     $out=$row;
 }
     $location = implode(',',$out);

    return  $location;
} 
  
  #################################################
   //This function used to get user retailer gift deatils
    public function get_user_retailer_gift_list($filter = '', $records = '', $orderby = '') {
        global $dbc;
        $out = array();
        $filterstr = $this->oo_filter($filter, $records, $orderby);

        $q = "SELECT * FROM user_retailer_gift_details  $filterstr";

        list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = '');
        if (!$opt)
            return $out;

        $gift_map = get_my_reference_array('_retailer_mkt_gift', 'id', 'gift_name');
        while ($row = mysqli_fetch_assoc($rs)) {
            $id = $row['order_id'];
            $out[$id] = $row; // storing the item id
            $out[$id]['gift_name'] = $gift_map[$row['gift_id']];
        }// while($row = mysqli_fetch_assoc($rs)){ ends
        return $out;
    }

    public function get_sale_order_mobile_list($filter = '', $records = '', $orderby = '') {
        global $dbc;
        $out = array();
        $filterstr = $this->oo_filter($filter, $records, $orderby);
        $mtype = $_SESSION[SESS . 'constant']['retailer_level'];
        $q = "SELECT * FROM user_sales_order_details $filterstr";
        //h1($q);
        list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = '');
        if (!$opt)
            return $out;
        $retailer_map = get_my_reference_array('retailer', 'id', 'firm_name');
        $brand_map = get_my_reference_array('catalog_1', 'id', 'name');
        while ($row = mysqli_fetch_assoc($rs)) {
            $id = $row['order_id'];
            $out[$id] = $row; // storing the item id
            $out[$id]['order_item'] = $this->get_my_reference_array_direct("SELECT user_sale.*,c1.name AS brand,c2.name AS catname,cp.name AS size FROM user_sales_order_details  user_sale INNER JOIN catalog_1 c1 ON c1.id = user_sale.catalog_1_id INNER JOIN catalog_2 c2 ON user_sale.catalog_2_id = c2.id INNER JOIN catalog_product cp ON user_sale.catalog_product_id = cp.id WHERE order_id = $id", 'id');
        }// while($row = mysqli_fetch_assoc($rs)){ ends
        return $out;
    }

    public function get_user_location($id, $date) {
        global $dbc;
        $out = NULL;
        $q = "SELECT location_5.name as name FROM location_5 INNER JOIN user_sales_order ON location_5.id = user_sales_order.location_id   WHERE user_sales_order.user_id = '$id' AND date='$date' GROUP BY location_5.id";
        //echo $q;
        $res = mysqli_query($dbc, $q);

        while ($row = mysqli_fetch_array($res)) {
            $out[] = $row['name'];
        }
        $out = implode(',', $out);
        //$out = $row['name'];
        return $out;
    }

    public function get_user_attendance($id, $date) {
        global $dbc;
        $out = NULL;
        $q = "SELECT ws.name As work_status_name FROM user_daily_attendance uda INNER JOIN _working_status ws ON ws.id = uda.work_status WHERE user_id = '$id' AND DATE_FORMAT(work_date,'%Y-%m-%d')='$date'";
        //echo $q;
        $res = mysqli_query($dbc, $q);

        while ($row = mysqli_fetch_array($res)) {
            $out[] = $row['work_status_name'];
        }
        $out = implode(',', $out);
        //$out = $row['name'];
        return $out;
    }

    public function get_user_expense($id, $date) {
        global $dbc;
        $out = NULL;
        $q = "SELECT travelling_allowance+drawing_allowance+other_expense AS expense FROM user_expense_report WHERE person_id = '$id' AND submit_date = '$date' LIMIT 1";
        //echo $q;
        $res = mysqli_query($dbc, $q);

        while ($row = mysqli_fetch_array($res)) {
            $out[] = $row['expense'];
        }
        $out = implode(',', $out);
        //$out = $row['name'];
        return $out;
    }

    public function get_user_wise_sale_data($id, $role_id) {
        global $dbc;
        $out = array();
        $main_id = $id;
        if ($role_id == 1) {
            $q = "SELECT user_id FROM user_sales_order ORDER BY id DESC";
            list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = '');
            if (!$opt)
                return $out;
            while ($row = mysqli_fetch_assoc($rs)) {
                $id = $row['user_id'];
                $out[$id] = $id; // storing the item id
            }// while($row = mysqli_fetch_assoc($rs)){ ends
        } // if($role_id == 1) end here
        else {
            /*$q = "SELECT role_id FROM _role WHERE role_id = '$role_id' OR senior_role_id >= '$role_id' AND role_group_id = '11'";
            list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = '');
            if (!$opt)
                return $out;
            $role_id_array = array();
            //$role_id_array[$role_id] = $role_id;
            while ($row = mysqli_fetch_assoc($rs)) {
                $role_id_array[$row['role_id']] = $row['role_id'];
            }
            $out[$main_id] = $main_id;
            $role_id_str = implode(',', $role_id_array);*/
           // $this->recursiveall2()
           // echo 'djskgsdlkfjlksjflsj';die;
                $this->recursiveall2($id);
                $juniors = join(',',$_SESSION['juniordata']);
                $_SESSION['juniordata']='';
                if(empty($juniors)){

                  $juniors=0;
                }
                 $junr = "person.id IN($id,$juniors)";
               
            $q = "SELECT person.id FROM person INNER JOIN user_sales_order ON user_sales_order.user_id = person.id WHERE $junr";
           // echo $q;die;
            list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = '');
            if (!$opt)
                return $out;
            while ($row = mysqli_fetch_assoc($rs)) {
                $out[$row['id']] = $row['id'];
            }
        }

        return $out;
    }

    public function get_user_wise_primary_sale_data($id, $role_id) {
        global $dbc;
        $out = array();
        $main_id = $id;
        if ($role_id == 1) {
            $q = "SELECT created_person_id FROM user_primary_sales_order ORDER BY id DESC";
            list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = '');
            if (!$opt)
                return $out;
            while ($row = mysqli_fetch_assoc($rs)) {
                $id = $row['created_person_id'];
                $out[$id] = $id; // storing the item id
            }// while($row = mysqli_fetch_assoc($rs)){ ends
        } // if($role_id == 1) end here
        else {
            $q = "SELECT role_id FROM _role WHERE role_id = '$role_id' OR senior_role_id >= '$role_id' AND role_group_id = '11'";
            list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = '');
            if (!$opt)
                return $out;
            $role_id_array = array();
            //$role_id_array[$role_id] = $role_id;
            while ($row = mysqli_fetch_assoc($rs)) {
                $role_id_array[$row['role_id']] = $row['role_id'];
            }
            $out[$main_id] = $main_id;
            $role_id_str = implode(',', $role_id_array);
            $q = "SELECT person.id FROM person INNER JOIN user_primary_sales_order ON user_primary_sales_order.created_person_id = person.id WHERE role_id IN ($role_id_str) AND person_id_senior = '$main_id'";
            list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = '');
            if (!$opt)
                return $out;
            while ($row = mysqli_fetch_assoc($rs)) {
                $out[$row['id']] = $row['id'];
            }
        }

        return $out;
    }

    public function get_user_wise_expense_data($id, $role_id) {
        global $dbc;
        $out = array();
        $main_id = $id;
        if ($role_id == 1) {
            $q = "SELECT person_id FROM user_expense_report ORDER BY id DESC";
            list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = '');
            if (!$opt)
                return $out;
            while ($row = mysqli_fetch_assoc($rs)) {
                $id = $row['person_id'];
                $out[$id] = $id; // storing the item id
            }// while($row = mysqli_fetch_assoc($rs)){ ends
        } // if($role_id == 1) end here
        else {
            /*$q = "SELECT role_id FROM _role WHERE role_id = '$role_id' OR senior_role_id >= '$role_id' AND role_group_id = '11'";
            list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = '');
            if (!$opt)
                return $out;
            $role_id_array = array();
            //$role_id_array[$role_id] = $role_id;
            while ($row = mysqli_fetch_assoc($rs)) {
                $role_id_array[$row['role_id']] = $row['role_id'];
            }
            $out[$main_id] = $main_id;
            $role_id_str = implode(',', $role_id_array);*/

             $this->recursiveall2($id);
                $juniors = join(',',$_SESSION['juniordata']);
                $_SESSION['juniordata']='';
                if(empty($juniors)){

                  $juniors=0;
                }
                 $junr = "person.id IN($id,$juniors)";


            $q = "SELECT person.id FROM person INNER JOIN user_expense_report ON user_expense_report.person_id = person.id WHERE $junr";
            //h1($q);die;
            list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = '');
            if (!$opt)
                return $out;
            while ($row = mysqli_fetch_assoc($rs)) {
                $out[$row['id']] = $row['id'];
            }
        }

        return $out;
    }

    public function get_user_wise_tracking_data($id, $role_id) {
        global $dbc;
        $out = array();
        $main_id = $id;
        if ($role_id == 1) {
            $q = "SELECT user_id FROM user_daily_tracking ORDER BY id DESC";
           // h1($q);
           // pre($_POST);
            list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = '');
            if (!$opt)
                return $out;
            while ($row = mysqli_fetch_assoc($rs)) {
                $id = $row['user_id'];
                $out[$id] = $id; // storing the item id
            }// while($row = mysqli_fetch_assoc($rs)){ ends
        } // if($role_id == 1) end here
        else {
            $q = "SELECT role_id FROM _role WHERE role_id = '$role_id' OR senior_role_id >= '$role_id' AND role_group_id = '11'";
            list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = '');
            if (!$opt)
                return $out;
            $role_id_array = array();
            //$role_id_array[$role_id] = $role_id;
            while ($row = mysqli_fetch_assoc($rs)) {
                $role_id_array[$row['role_id']] = $row['role_id'];
            }
            $out[$main_id] = $main_id;
            $role_id_str = implode(',', $role_id_array);
            $q = "SELECT person.id FROM person INNER JOIN user_daily_tracking ON user_daily_tracking.user_id = person.id WHERE role_id IN ($role_id_str) AND person_id_senior = '$main_id'";
            list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = '');
            if (!$opt)
                return $out;
            while ($row = mysqli_fetch_assoc($rs)) {
                $out[$row['id']] = $row['id'];
            }
        }

        return $out;
    }

    public function next_order_num() {
        global $dbc;
        $out = array();
        $q = "SELECT MAX(id) AS total FROM user_sales_order";
        list($opt, $rs) = run_query($dbc, $q, $mode = 'single', $msg = '');
        //return $rs['total']+1;	
    }

    public function get_username($id) {
        global $dbc;
        $out = NULL;
        $q = "SELECT CONCAT_WS(' ',first_name,middle_name,last_name) AS uname FROM person WHERE id = $id";
        //h1($q);
        list($opt, $rs) = run_query($dbc, $q, $mode = 'single', $msg = '');
        if (!$opt)
            return $out;
        return $rs['uname'];
    }
     public function get_location($id) {
        global $dbc;
        $out1 = array();
        $q = "SELECT * FROM `location_view` WHERE `l5_id` = $id";
      //  echo $q;
        $qr = mysqli_query($dbc,$q);
        while($row = mysqli_fetch_assoc($qr))
        {
            $out1['State'] = $row['l2_name'];
             $out1['City'] = $row['l3_name'];
              $out1['District'] = $row['l4_name'];
              $out1['Beat'] = $row['l5_name'];
              
            
        }
       // pre($out1);
        //echo key($out1);
        return $out1;
    }

    public function get_sale_order_temp_se_data() {
        $d1 = $_POST;
        $d1['uid'] = $_SESSION[SESS . 'data']['id'];
        $d1['myreason'] = 'Please fill all the required information';
        $d1['what'] = 'Sale Order'; //whether to do history log or not
        return array(true, $d1);
    }


    public function get_sale_order_temp_list($filter = '', $records = '', $orderby = '') {
        global $dbc;
        $out = array();
        $filterstr = $this->oo_filter($filter, $records, $orderby);

        $q = "SELECT * FROM user_sale_order_details_temp $filterstr";
        list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = '');
        if (!$opt)
            return $out;
        $retailer_map = get_my_reference_array('retailer', 'id', 'firm_name');
        $brand_map = get_my_reference_array('catalog_1', 'id', 'name');
        while ($row = mysqli_fetch_assoc($rs)) {
            $id = $row['id'];
            $out[$id] = $row; // storing the item id
            $out[$id]['firm_name'] = $retailer_map[$row['retailer_id']];
            $out[$id]['brand_name'] = $brand_map[$row['catalog_1_id']];
            $out[$id]['user_name'] = $this->get_username($row['user_id']);
        }// while($row = mysqli_fetch_assoc($rs)){ ends
        return $out;
    }

    public function sale_delete($id, $filter = '', $records = '', $orderby = '') {
            global $dbc;
            if (empty($filter))
                $filter = "itemId = $id";
            $out = array('status' => false, 'myreason' => '');
            $del_query="SELECT uso.order_id FROM user_sales_order AS uso INNER JOIN user_sales_order_details AS usod ON uso.order_id=uso.order_id WHERE uso.order_id='$id' ";
            
            $run_del_query = mysqli_query($dbc,$del_query);
            $deleteRecord= mysqli_num_rows($run_del_query);
            //$deleteRecord = $this->get_sale_list($filter = "order_id=$id", $records, $orderby);
            if ($deleteRecord<=0) {
                $out['myreason'] = 'user sales order not found';
                return $out;
            }
            //start the transaction
            mysqli_query($dbc, "START TRANSACTION");

            //Running the deletion queries
            $delquery = array();
            $delquery['user_sales_order'] = "DELETE FROM user_sales_order WHERE order_id = $id ";
            $delquery['user_sales_order_details'] = "DELETE FROM user_sales_order_details WHERE order_id = $id";
            foreach ($delquery as $key => $value) {
                if (!mysqli_query($dbc, $value)) {
                    mysqli_rollback($dbc);
                    return array('status' => false, 'myreason' => '$key query failed');
                }
            }
            //After successfull deletion
            mysqli_commit($dbc);
            return array('status' => true, 'myreason' => 'Sales Order successfully deleted');
        }

    public function sale_order_temp_delete() {
        global $dbc;

        $temp_id = $_POST['chk'];

        if (!empty($temp_id)) {
            $temp_id_array = implode(',', $temp_id);
            mysqli_query($dbc, "START TRANSACTION");
            $q = "DELETE FROM user_sale_order_details_temp WHERE id IN ($temp_id_array)";
            $r = mysqli_query($dbc, $q);
            if (!$r) {
                mysqli_rollback($dbc);
                return array('status' => false, 'myreason' => 'Some error occurred in deletion please try again later');
            }
            return array('status' => TRUE, 'myreason' => 'Sale Order data deleted succesfully');
            mysqli_commit($dbc);
        } else {
            return array('status' => false, 'myreason' => 'Deletion failed please select any data for deletion');
        }
    }

    public function get_user_tracking_list1($filter = '', $records = '', $orderby = '') {
        global $dbc;
        $out = array();
        $pkeyid = array();
        $filterstr = $this->oo_filter($filter, $records, $orderby);
        $q = "SELECT * FROM user_daily_tracking $filterstr";

        list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = '');
        if (!$opt)
            return $out;
        while ($row = mysqli_fetch_assoc($rs)) {
            $pkeyid[] = $row['id'];
        }// while($row = mysqli_fetch_assoc($rs)){ ends
        $pkeyidarray = implode(',', $pkeyid);
        $q1 = "SELECT * FROM user_daily_tracking  $filterstr";
       // h1($q1);
        list($opt1, $rs1) = run_query($dbc, $q1, $mode = 'multi', $msg = '');
        while ($rows = mysqli_fetch_assoc($rs1)) {
            $id = $rows['user_id'];
            $out[$id] = $rows;
            $out[$id]['user_name'] = $this->get_username($rows['user_id']);
            $out[$id]['track_details'] = $this->get_my_reference_array_direct("SELECT * FROM user_daily_tracking WHERE id IN ($pkeyidarray)", 'id');
        }
        return $out;
    }

    public function get_user_tracking_list($filter = '', $records = '', $orderby = '') {
        global $dbc;
        $out = array();
        $pkeyid = array();
        $filterstr = $this->oo_filter($filter, $records, $orderby);
        $limit = $_SESSION[SESS . 'constant']['tracking_count'];
       /* $q = "SELECT *,DATE_FORMAT(track_date,'%Y%m%d') AS tdate,CONCAT_WS(' ',first_name,middle_name,last_name) AS user_name FROM user_daily_tracking INNER JOIN person ON person.id=user_daily_tracking.user_id"
            . " INNER JOIN user_dealer_retailer udr ON udr.user_id=person.id INNER JOIN dealer_location_rate_list dlrl ON dlrl.user_id=udr.dealer_id"
            . " INNER JOIN location_view lv ON lv.l5_id=dlrl.location_id $filterstr GROUP BY person.id,track_date ORDER BY track_date ASC,l1_name ASC";
        */
          $q = "SELECT *,DATE_FORMAT(track_date,'%Y%m%d') AS tdate,l4_id,CONCAT_WS(' ',first_name,middle_name,last_name) AS user_name "
                  . "FROM user_daily_tracking "
                  . "INNER JOIN person ON person.id=user_daily_tracking.user_id"
            . " INNER JOIN dealer_location_rate_list dlrl ON dlrl.user_id=user_daily_tracking.user_id "
           //       . "INNER JOIN dealer ON dealer.id=dlrl.dealer_id "
            . " INNER JOIN location_view lv ON lv.l5_id=dlrl.location_id $filterstr";
      //  h1($q);
        
        list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = '');
        if (!$opt)
            return $out;

        while ($rows = mysqli_fetch_assoc($rs)) {
            $id = $rows['user_id'].$rows['tdate'];
            $out[$id] = $rows;
            $out[$id]['track_details'] = $this->get_my_reference_array_direct("SELECT * FROM user_daily_tracking WHERE user_id = '$rows[user_id]' AND track_date = '$rows[track_date]' LIMIT $limit", 'id');
        }
        return $out;
    }

    public function get_user_tracking_distance_list($filter = '', $records = '', $orderby = '') {
        global $dbc;
        $out = array();
        $pkeyid = array();
        $filterstr = $this->oo_filter($filter, $records, $orderby);
        $limit = $_SESSION[SESS . 'constant']['tracking_count'];
        $q = "SELECT *,DATE_FORMAT(track_date,'%Y%m%d') AS tdate FROM user_daily_tracking INNER JOIN person ON person.id=user_daily_tracking.user_id $filterstr";
        //h1($q);
        list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = '');
        if (!$opt)
            return $out;

        while ($rows = mysqli_fetch_assoc($rs)) {
            $id = $rows['track_date'] . $rows['user_id'];
            $out[$id] = $rows;
            $out[$id]['user_name'] = $this->get_username($rows['user_id']);
            $out[$id]['lat_long'] = $this->get_final_latlong_details($rows['user_id'], $rows['track_date']);
        }

        return $out;
    }

    public function get_distributor_view_list($filter = '', $records = '', $orderby = '') {
        // print_r($filter);exit;
          global $dbc;
        $out = array();       
        // if user has send some filter use them.
        $filterstr = $this->oo_filter($filter, $records, $orderby);
        $q = "SELECT person_name,dealer_id, lastvisit,lastlogout FROM dealer_person_login  $filterstr ";
        //h1($q);
        $rs = mysqli_query($dbc, $q);      
        while ($row = mysqli_fetch_assoc($rs)) {
          //  $id = $row['dealer_id'];
            $out['dealer_id'] = $row['dealer_id'];
             $out['person_name'] = $row['person_name'];
            $out['lastvisit'] = $row['lastvisit'];
            $out['lastlogout'] = $row['lastlogout'];
            
           
        }
        // pre($out);
        return $out;
     }
    //user_tracking_distance
    public function get_final_latlong_details($user_id, $track_date) {
        global $dbc;
        $out = array();
        $limit = $_SESSION[SESS . 'constant']['tracking_count'];
        $q = "SELECT * FROM user_daily_tracking WHERE user_id = '$user_id' AND track_date = '$track_date' LIMIT $limit";

        list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = '');
        if (!$opt)
            return $out;
        while ($rows = mysqli_fetch_assoc($rs)) {
            $ltlg = explode(',', $rows['lat_lng']);
            if (($ltlg[0] != '0.0') && ($ltlg[1] != '0.0')) {
                $latlong = $rows['lat_lng'];
            } else {
                $mmc_mnc_lat_cellid = explode(':', $rows['mnc_mcc_lat_cellid']);
                $latlong = getlatlongbymccmnclaccid($mmc_mnc_lat_cellid[0], $mmc_mnc_lat_cellid[1], $mmc_mnc_lat_cellid[2], $mmc_mnc_lat_cellid[3]);
            }
            $out[] = $latlong;
        }
        return $out;
    }

    public function get_branch_staff_list($filter = '', $records = '', $orderby = '') {
        global $dbc;
        $out = array();
        $filterstr = $this->oo_filter($filter, $records, $orderby);
        $q = "SELECT *,DATE_FORMAT(dob,'%d/%m/%Y') AS dob,DATE_FORMAT(anniversary,'%d/%m/%Y') AS anniversary FROM branch_staff_detail $filterstr";
        //h1($q);
        list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = '');
        if (!$opt)
            return $out;
        while ($row = mysqli_fetch_assoc($rs)) {
            $id = $row['id'];
            $out[$id] = $row; // storing the item id
            $out[$id]['person_name'] = $this->get_username($row['submit_person_id']);
        }// while($row = mysqli_fetch_assoc($rs)){ ends
        return $out;
    }

    public function get_all_user_list($filter = '', $records = '', $orderby = '') {
        global $dbc;
        $out = array();
        $filterstr = $this->oo_filter($filter, $records, $orderby);
        $loc_level = $_SESSION[SESS . 'constant']['location_level'];
        $q = "SELECT user_id FROM `location_$loc_level` l2 INNER JOIN dealer_location_rate_list dlrl ON dlrl.location_id = l2.id INNER JOIN user_dealer_retailer USING(dealer_id) $filterstr ";             //h1($q);
        list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = '');
        if (!$opt)
            return $out;
        while ($row = mysqli_fetch_assoc($rs)) {
            $id = $row['user_id'];
            $out[] = $id; // storing the item id
        }// while($row = mysqli_fetch_assoc($rs)){ ends
        $out = implode(',', $out);

        return $out;
    }

    //This function will help in the mulitpage print
    // This function is used to set automatic path for all the document
    public function get_document_type_list($filter = '', $records = '', $orderby = '') {
        global $dbc;
        $out = array();
        //if user has send some filter use them.
        $filterstr = $this->oo_filter($filter, $records, $orderby);
        $q = "SELECT * FROM _document_type $filterstr";
        list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = '');
        if (!$opt)
            return $out; // if no order placed send blank array
        while ($row = mysqli_fetch_assoc($rs)) {
            $id = $row['id'];
            $out[$id] = $row; // storing the item id
        }
        return $out;
    }

    // This function is used to get last level catalog id
    public function get_last_level_catalog_id($count, $last_level_id, $catalog_level) {
        global $dbc;
        $out = array();

        $q = "SELECT catalog_product.id, catalog_product.name FROM catalog_$count";
        for ($i = $count; $i < $catalog_level; $i++) {
            $j = $i + 1;
            $q .= " INNER JOIN catalog_$j ON catalog_$j.catalog_" . $i . "_id = catalog_$i.id ";
        }
        $q .= " INNER JOIN catalog_product ON catalog_product.catalog_id = catalog_$catalog_level.id WHERE catalog_$count.id = '$last_level_id'";

        list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = '');
        if (!$opt)
            return $out;

        while ($row = mysqli_fetch_assoc($rs)) {

            $out[] = $row['id']; // storing the item id
        }// while($row = mysqli_fetch_assoc($rs)){ ends

        return $out;
    }

    // Here we get last level location id
    public function get_last_level_location_id($count, $last_level_id, $location_level) {
        global $dbc;
        $out = array();
        $q = "SELECT user_id FROM location_$count";
        for ($i = $count; $i < $location_level; $i++) {
            $j = $i + 1;
            $q .= " INNER JOIN location_$j ON location_$j.location_" . $i . "_id = location_$i.id ";
        }
        $q .= " INNER JOIN dealer_location_rate_list ON dealer_location_rate_list.location_id = location_$location_level.id INNER JOIN user_dealer_retailer USING(dealer_id) WHERE location_$count.id = '$last_level_id'";
//h1($q);
        list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = '');
        if (!$opt)
            return $out;

        while ($row = mysqli_fetch_assoc($rs)) {

            $out[$row['user_id']] = $row['user_id']; // storing the item id
        }// while($row = mysqli_fetch_assoc($rs)){ ends

        return $out;
    }
public function get_last_level_location_ids($count, $last_level_id, $location_level) {
        global $dbc;
        $out = array();
        $q = "SELECT user_id FROM location_$count";
        for ($i = $count; $i < $location_5; $i++) {
            $j = $i + 1;
            $q .= " INNER JOIN location_$j ON location_$j.location_" . $i . "_id = location_$i.id ";
        }
        $q .= " INNER JOIN dealer_location_rate_list ON dealer_location_rate_list.location_id = location_$location_level.id INNER JOIN user_dealer_retailer USING(dealer_id) WHERE location_$count.id = '$last_level_id'";
//h1($q);
        list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = '');
        if (!$opt)
            return $out;

        while ($row = mysqli_fetch_assoc($rs)) {

            $out[$row['user_id']] = $row['user_id']; // storing the item id
        }// while($row = mysqli_fetch_assoc($rs)){ ends

        return $out;
    }
    
    public function get_challan_list($filter = '', $records = '', $orderby = '') {
        global $dbc;
        $out = array();
       // pre($filter);die;
        $filterstr = $this->oo_filter($filter, $records, $orderby);
     $q = "SELECT *, DATE_FORMAT(dispatch_date, '" . MASKDATE . "') AS dispatch_date, DATE_FORMAT(ch_date, '%Y-%m-%d') AS ch_date FROM challan_order $filterstr";
        //h1($q);
        list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = '');
        if (!$opt)
            return $out;
        $dealer_map = get_my_reference_array('dealer', 'id', 'name');
        $retailer_map = get_my_reference_array('retailer', 'id', 'name');
        while ($row = mysqli_fetch_assoc($rs)) {
            $id = $row['id'];
            $out[$id] = $row; // storing the item id
            $out[$id]['dealer_name'] = $dealer_map[$row['ch_dealer_id']];
            $out[$id]['retailer_name'] = $retailer_map[$row['ch_retailer_id']];
          $q = "SELECT challan_order_details.*,challan_order_details.product_id AS pid, catalog_product.name FROM challan_order_details INNER JOIN catalog_product ON catalog_product.id = challan_order_details.product_id  INNER JOIN catalog_2 c2 ON c2.id = catalog_product.catalog_id  WHERE ch_id = $id ";
            $out[$id]['challan_item'] = $this->get_my_reference_array_direct($q, 'id');
        }// while($row = mysqli_fetch_assoc($rs)){ ends
        return $out;
    }

     public function recursiveall2($code) {
        global $dbc;
//static $data;
        $qry = "";
        $res1 = "";
        $res2 = "";
        $qry = mysqli_query($dbc, "select id  from person where person_id_senior=trim('" . $code . "')");
        $num = mysqli_num_rows($qry);
        if ($num <= 0) {
            $res1 = mysqli_fetch_assoc($qry);
            if ($res1['id'] != "") {
                $_SESSION['juniordata'][] = "'" . $res1['id'] . "'";
            }
        } else {
            while ($res2 = mysqli_fetch_assoc($qry)) {
                if ($res2['id'] != "") {
                    $_SESSION['juniordata'][] = "'" . $res2['id'] . "'";
                    $this->recursiveall2($res2['id']);
                }
            }
        }
    }

    /*public function get_time_report_list($filter = '', $records = '', $orderby = '', $date_filter = '',$filter2) {
        global $dbc;
        $out = array();
        $month = $filter['month'];
        $rol_fltr = isset($filter['rol_fltr'])?$filter['rol_fltr']:'1';
        $isr_fltr = isset($filter['no_isr'])?$filter['no_isr']:'1';
        $role_id = isset($filter['role_id'])?$filter['role_id']:'1';
       array_shift($filter);
     //pre($filter);die;
        $filterstr = $this->oo_filter($filter2, $records, $orderby);
     // h1($filterstr);die;
        // $date_filterstr = $this->oo_filter($date_filter, $records='', $orderby='');     
        $q = "SELECT stateid,statename from state $filterstr limit 1 ";
       // echo $q;die;
        $filterstr=$filterstr.' AND '.$rol_fltr." AND ".$isr_fltr." AND ".$role_id;
     
        list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = '');
        if (!$opt)
            return $out;
        while ($row = mysqli_fetch_assoc($rs)) {
            $id = $row['stateid'];
            $out[$id]['State'] = $row['statename'];
            $out[$id]['details'] = $this->get_time_report_details($filterstr, $month);
        }
      //pre($out);
        return $out;
    }
    

    public function get_time_report_details($filterstr, $month) {
        //echo $filterstr;die;
        global $dbc;
        $out = array();
        $q = "SELECT emp_code,person.role_id as role,CONCAT_WS(' ',person.first_name,person.middle_name,person.last_name)as user_name,person.id as p_id,statename
            FROM person INNER JOIN person_login ON person_login.person_id=person.id AND person_login.person_status='1' INNER JOIN state ON state.stateid=person.state_id INNER JOIN _role ON _role.role_id= person.role_id  $filterstr  GROUP BY person.id ORDER BY statename ASC";
      // h1($q);die;
        list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = '');
        if (!$opt)
            return $out;

        while ($row = mysqli_fetch_assoc($rs)) {
            $holder = array();
            $id = $row['s_id'] . $row['p_id'];
            //$out[$id] = $row; // storing the item id
            $out[$id]['p_id'] = $row['p_id'];
            
            $out[$id]['state'] = $row['statename'];
            $out[$id]['emp_code'] = $row['emp_code'];
            $out[$id]['name'] = $row['user_name'];
            $out[$id]['role'] = myrowval($table = "_role", $col = "rolename", $where = "role_id = '$row[role]'");
            $out[$id]['weekly_off'] = '9';
            $out[$id]['days'] = $this->get_total_dates($month);
            $holder = $this->get_total_attendance($row['p_id'], $month);
            //pre($holder);
            $out[$id]['attendance'] = $holder['attendance'];
            $out[$id]['a_time'] = $holder['a_time'];
            $out[$id]['b_time'] = $holder['b_time'];
            $out[$id]['days_time'] = $this->get_date_time($month, $row['p_id']);
            $holder = "";
        }
     //pre($out);
        return $out;
    }
    public function get_total_dates($date) {
        global $dbc;
        $diff = NULL;
        $curdate = date('mY');
        if ($date == $curdate) {
            $no_of_cur_days = date('d');
        } else {

            $no_of_cur_days = $this->get_no_days_in_month($date);
        }
        //h1($no_of_cur_days);  

        return $no_of_cur_days;
    }

    public function get_no_days_in_month($monthyear){

        $year = substr($monthyear, 2, 4);
         $month = substr($monthyear, 0, 2);
       $days =  cal_days_in_month(CAL_GREGORIAN,$month,$year);
       // $month = substr($monthyear, 0, 2);
       return $days;
    }


    public function get_date_time($date, $person) {
        global $dbc;
        $hold = array();
        $q = " select DATE_FORMAT(user_daily_attendance.work_date,'%Y-%m-%d') as date,DATE_FORMAT(check_out.work_date,'%H:%i:%s') as check_out_time,DATE_FORMAT(user_daily_attendance.work_date,'%H:%i:%s') as time,user_daily_attendance.work_status,ws.name wname from user_daily_attendance INNER JOIN _working_status ws ON ws.id=user_daily_attendance.work_status LEFT JOIN check_out ON DATE_FORMAT(check_out.work_date,'%Y-%m-%d')= DATE_FORMAT(user_daily_attendance.work_date,'%Y-%m-%d') AND check_out.user_id='$person' where user_daily_attendance.user_id ='$person' AND  DATE_FORMAT(user_daily_attendance.work_date,'%m%Y') = '$date' ";
       //h1($q);
        list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = '');
        if (!$opt)
            return $out;
        while ($row = mysqli_fetch_assoc($rs)) {
            //$id = $row['id'];
            $hold[$row['date']] = array('time'=>$row['time'],'status'=>$row['work_status'],'wname'=>$row['wname'],'check_out_time'=>$row['check_out_time']);
        }
        //pre($hold);
        return $hold;
    }
    public function get_total_attendance($person, $month) {
        global $dbc;
        $data = array();
        $q = "select (SELECT COUNT(id) from user_daily_attendance where user_id = '$person' AND DATE_FORMAT(work_date,'%m%Y') = '$month') as attendance, "
                . "(SELECT COUNT(id) from user_daily_attendance where user_id = '$person' AND DATE_FORMAT(work_date,'%m%Y') = '$month' AND DATE_FORMAT(work_date,'%H%i%s') <= '09:30:00') as b_time,"
                . "(SELECT COUNT(id) from user_daily_attendance where user_id = '$person' AND DATE_FORMAT(work_date,'%m%Y') = '$month' AND DATE_FORMAT(work_date,'%H%i%s') > '09:30:00') as a_time  ";
       // h1($q);
        $q_res = mysqli_query($dbc, $q);
        while ($q_row = mysqli_fetch_array($q_res)) {
            //pre($q_row);
            $data['attendance'] = $q_row['attendance'];
            $data['a_time'] = $q_row['a_time'];
            $data['b_time'] = $q_row['b_time'];
        }
        // pre($data);
        return $data;
    }*/
    public function time_report_edit($id) {
        global $dbc;
        $out = array('status' => 'false', 'myreason' => '');
        list($status, $d1) = $this->get_sale_order_se_data();
        if (!$status)
            return array('staus' => false, 'myreason' => $d1['myreason']);
        $orderno = $this->next_order_num();
        $total_sale_value = $this->get_sale_value($d1['catalog_1_id'], $d1['metric_ton']);
        //Start the transaction
        mysqli_query($dbc, "START TRANSACTION");
        // query to update
        $q = "UPDATE user_sales_order SET user_id='$d1[uid]',retailer_id = '$d1[retailer_id]',order_id='$id',call_status = '$d1[call_status]',total_sale_value = '$d1[total_sale_value]',date = NOW(),time = NOW(), company_id = '{$_SESSION[SESS . 'data']['company_id']}', remarks= '$d1[remarks]' WHERE id = '$id'";

        $r = mysqli_query($dbc, $q);
        if (!$r) {
            mysqli_rollback($dbc);
            return array('status' => false, 'myreason' => 'Sales Could not be updated, some error occurred');
        }
        $rId = $id;

        return array('status' => true, 'myreason' => $d1['what'] . ' successfully Updated');
    }
    public function get_time_report_list($filter = '', $records = '', $orderby = '', $date_filter = '',$filter2) {
            global $dbc;
            $out = array();
             $datearray = array();
              $start = get_mysql_date($_POST['start'],'/',$time = false, $mysqlsearch = true);
              $end = get_mysql_date($_POST['end'],'/',$time = false, $mysqlsearch = true);
          //  $month = $filter['month'];
      //    pre($filter);//die;
            $rol_fltr = isset($filter['rol_fltr'])?$filter['rol_fltr']:'1';
            $isr_fltr = isset($filter['no_isr'])?$filter['no_isr']:'1';
            $role_id = isset($filter['role_id'])?$filter['role_id']:'1';
           
            if(!empty($_POST['status'])){
                $person_status= " AND person_status='$_POST[status]' ";
            }else{
                $person_status= " AND person_status='1' ";
            }
            //$person_status = isset($filter['status'])?$filter['status']:'1';
           array_shift($filter);
        
            $filterstr = $this->oo_filter($filter2, $records, $orderby);
         // h1($filterstr);die;
            // $date_filterstr = $this->oo_filter($date_filter, $records='', $orderby='');     
            $q = "SELECT stateid,statename from state $filterstr limit 1 ";
           // echo $q;die;
            $filterstr=$filterstr.' AND '.$rol_fltr." AND ".$isr_fltr." AND ".$role_id;
         
            list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = '');
            if (!$opt)
                return $out;
            while ($row = mysqli_fetch_assoc($rs)) {
                $id = $row['stateid'];
                $out[$id]['State'] = $row['statename'];
                $out[$id]['details'] = $this->get_time_report_details($filterstr,$start,$end ,$person_status);
            }
          //pre($out);
            return $out;
        }
        

        public function get_time_report_details($filterstr,$start,$end ,$person_status ) {
            //echo $filterstr;die;
            global $dbc;
            $out = array();
            $q = "SELECT emp_code,person.role_id as role,CONCAT_WS(' ',person.first_name,person.middle_name,person.last_name)as user_name,person.id as p_id,statename
                FROM person INNER JOIN person_login ON person_login.person_id=person.id  INNER JOIN state ON state.stateid=person.state_id INNER JOIN _role ON _role.role_id= person.role_id 
                 $filterstr $person_status GROUP BY person.id ORDER BY statename ASC";
    // h1($q);//die;
            list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = '');
            if (!$opt)
                return $out;

            while ($row = mysqli_fetch_assoc($rs)) {
                $holder = array();
                $id = $row['s_id'] . $row['p_id'];
                //$out[$id] = $row; // storing the item id
                $out[$id]['p_id'] = $row['p_id'];
                
                $out[$id]['state'] = $row['statename'];
                $out[$id]['emp_code'] = $row['emp_code'];
                $out[$id]['name'] = $row['user_name'];
                $out[$id]['role'] = myrowval($table = "_role", $col = "rolename", $where = "role_id = '$row[role]'");
                 $out[$id]['senior'] = myrowval($table = "person", $col = "CONCAT_WS(' ',person.first_name,person.middle_name,person.last_name)", $where = "person_id_senior = '$row[p_id]'");
                $out[$id]['weekly_off'] = '9';
                //$out[$id]['days'] = $this->get_total_dates($datediff);
                $holder = $this->get_total_attendance($row['p_id'], $start,$end);
                //pre($holder);
                $out[$id]['attendance'] = $holder['attendance'];
                $out[$id]['a_time'] = $holder['a_time'];
                $out[$id]['b_time'] = $holder['b_time'];
                $out[$id]['working'] = $holder['working'];
                $out[$id]['leave'] = $holder['leave'];
                $out[$id]['training'] = $holder['training'];
                $out[$id]['meeting'] = $holder['meeting'];
                $out[$id]['woff'] = $holder['woff'];
                $out[$id]['holiday'] = $holder['holiday'];
                $out[$id]['days_time'] = $this->get_date_time($start,$end,$row['p_id']);
                $holder = "";
            }
         //pre($out);
            return $out;
        }
          public function get_total_dates($date) {
            global $dbc;
            $diff = NULL;
            $curdate = date('mY');
            if ($date == $curdate) {
                $no_of_cur_days = date('d');
            } else {

                $no_of_cur_days = $this->get_no_days_in_month($date);
            }
            //h1($no_of_cur_days);  

            return $no_of_cur_days;
        }

        public function get_no_days_in_month($monthyear){

            $year = substr($monthyear, 2, 4);
             $month = substr($monthyear, 0, 2);
           $days =  cal_days_in_month(CAL_GREGORIAN,$month,$year);
           // $month = substr($monthyear, 0, 2);
           return $days;
        }


         public function get_date_time($start,$end,$person) {
            global $dbc;
            $hold = array();
            $q = " select DATE_FORMAT(user_daily_attendance.work_date,'%Y-%m-%d') as date,DATE_FORMAT(check_out.work_date,'%H:%i:%s') as check_out_time,DATE_FORMAT(user_daily_attendance.work_date,'%H:%i:%s') as time,user_daily_attendance.work_status,ws.name wname from user_daily_attendance INNER JOIN _working_status ws ON ws.id=user_daily_attendance.work_status LEFT JOIN check_out ON DATE_FORMAT(check_out.work_date,'%Y-%m-%d')= DATE_FORMAT(user_daily_attendance.work_date,'%Y-%m-%d') AND check_out.user_id='$person' where user_daily_attendance.user_id ='$person' AND  DATE_FORMAT(user_daily_attendance.work_date,'%Y%m%d') >= '$start' AND  DATE_FORMAT(user_daily_attendance.work_date,'%Y%m%d') <= '$end' ";
           //h1($q);
            list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = '');
            if (!$opt)
                return $out;
            while ($row = mysqli_fetch_assoc($rs)) {
                //$id = $row['id'];
                $hold[$row['date']] = array('time'=>$row['time'],'status'=>$row['work_status'],'wname'=>$row['wname'],'check_out_time'=>$row['check_out_time']);
            }
            //pre($hold);
            return $hold;
        }
        public function get_total_attendance($person,$start,$end) {
            global $dbc;
            $data = array();
            $q = "select (SELECT COUNT(id) from user_daily_attendance where user_id = '$person' AND DATE_FORMAT(work_date,'%Y%m%d') >= '$start' AND DATE_FORMAT(work_date,'%Y%m%d') <= '$end') as attendance, "
                    . "(SELECT COUNT(id) from user_daily_attendance where user_id = '$person' AND DATE_FORMAT(work_date,'%Y%m%d') >= '$start' AND DATE_FORMAT(work_date,'%%Y%m%d') <= '$end' AND DATE_FORMAT(work_date,'%H%i%s') <= '09:30:00') as b_time,"
                    . "(SELECT COUNT(id) from user_daily_attendance where user_id = '$person' AND DATE_FORMAT(work_date,'%Y%m%d') >= '$start' AND DATE_FORMAT(work_date,'%Y%m%d') <= '$end' AND DATE_FORMAT(work_date,'%H%i%s') > '09:30:00') as a_time,"
                    . "(SELECT COUNT(id) from user_daily_attendance where user_id = '$person' AND DATE_FORMAT(work_date,'%Y%m%d') >= '$start' AND DATE_FORMAT(work_date,'%Y%m%d') <= '$end' AND work_status=1) as working,"
                    . "(SELECT COUNT(id) from user_daily_attendance where user_id = '$person' AND DATE_FORMAT(work_date,'%Y%m%d') >= '$start' AND DATE_FORMAT(work_date,'%Y%m%d') <= '$end' AND work_status=12) as `leave`,"
                    . "(SELECT COUNT(id) from user_daily_attendance where user_id = '$person' AND DATE_FORMAT(work_date,'%Y%m%d') >= '$start' AND DATE_FORMAT(work_date,'%Y%m%d') <= '$end' AND work_status=14) as training,"
                    . "(SELECT COUNT(id) from user_daily_attendance where user_id = '$person' AND DATE_FORMAT(work_date,'%Y%m%d') >= '$start' AND DATE_FORMAT(work_date,'%Y%m%d') <= '$end' AND work_status=13) as meeting,"
                    . "(SELECT COUNT(id) from user_daily_attendance where user_id = '$person' AND DATE_FORMAT(work_date,'%Y%m%d') >= '$start' AND DATE_FORMAT(work_date,'%Y%m%d') <= '$end' AND work_status=5) as woff,"
                    . "(SELECT COUNT(id) from user_daily_attendance where user_id = '$person' AND DATE_FORMAT(work_date,'%Y%m%d') >= '$start' AND DATE_FORMAT(work_date,'%Y%m%d') <= '$end' AND work_status=15) as holiday";
            //h1($q);
            $q_res = mysqli_query($dbc, $q);
            while ($q_row = mysqli_fetch_array($q_res)) {
                //pre($q_row);
                $data['attendance'] = $q_row['attendance'];
                $data['a_time'] = $q_row['a_time'];
                $data['b_time'] = $q_row['b_time'];
                $data['working'] = $q_row['working'];
                $data['leave'] = $q_row['leave'];
                $data['training'] = $q_row['training'];
                $data['meeting'] = $q_row['meeting'];
                $data['woff'] = $q_row['woff'];
                $data['holiday'] = $q_row['holiday'];
            }
            // pre($data);
            return $data;
        }
        
        // public function get_total_attendance($person,$start,$end) {
        //     global $dbc;
        //     $data = array();
        //     $q = "select (SELECT COUNT(id) from user_daily_attendance where user_id = '$person' AND DATE_FORMAT(work_date,'%Y%m%d') >= '$start' AND DATE_FORMAT(work_date,'%Y%m%d') <= '$end') as attendance, "
        //             . "(SELECT COUNT(id) from user_daily_attendance where user_id = '$person' AND DATE_FORMAT(work_date,'%Y%m%d') >= '$start' AND DATE_FORMAT(work_date,'%%Y%m%d') <= '$end' AND DATE_FORMAT(work_date,'%H%i%s') <= '09:30:00') as b_time,"
        //             . "(SELECT COUNT(id) from user_daily_attendance where user_id = '$person' AND DATE_FORMAT(work_date,'%Y%m%d') >= '$start' AND DATE_FORMAT(work_date,'%Y%m%d') <= '$end' AND DATE_FORMAT(work_date,'%H%i%s') > '09:30:00') as a_time  ";
        //     //h1($q);
        //     $q_res = mysqli_query($dbc, $q);
        //     while ($q_row = mysqli_fetch_array($q_res)) {
        //         //pre($q_row);
        //         $data['attendance'] = $q_row['attendance'];
        //         $data['a_time'] = $q_row['a_time'];
        //         $data['b_time'] = $q_row['b_time'];
        //     }
        //     // pre($data);
        //     return $data;
        // }
    
    
     public function get_sale_claim_list($filter = '', $records = '', $orderby = '') {
           global $dbc;
        $out = array();
        //if user has send some filter use them.
        $filterstr = $this->oo_filter($filter, $records, $orderby);
        $q = "SELECT id,(select name from dealer where id=claim_challan.dealer_id) as dealer_name,claim_amount,claim,DATE_FORMAT(claim_date,'%d-%m-%Y') as claim_date,total_amt,status FROM claim_challan $filterstr";
        list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = '');
        if (!$opt)
            return $out; // if no order placed send blank array
        while ($row = mysqli_fetch_assoc($rs)) {
            $id = $row['id'];
            $out[$id] = $row; 
            $out[$id]['dealer_name'] = $row['dealer_name'];
            $out[$id]['claim_amount'] = $row['claim_amount'];
            $out[$id]['claim'] = $row['claim'];
            $out[$id]['claim_date'] = $row['claim_date'];
            $out[$id]['total_amt'] = $row['total_amt'];
            $out[$id]['status'] = $row['status'];
        }
       // print_r($out);
        return $out;
    }
    
      public function get_sales_team_attendance_list($filter = '', $records = '', $orderby = '',$date) {
        global $dbc;
        $filterstr = $this->oo_filter($filter, $records,$orderby);
        $out = NULL;
        $q = "SELECT rolename,l4_id as zone_id,person.id as user_id,CONCAT_WS(' ',first_name,last_name) as pname FROM person "
            . " INNER JOIN person_login pl ON pl.person_id=person.id"
            . " INNER JOIN _role USING(role_id) INNER JOIN dealer_location_rate_list dlrl ON dlrl.dealer_id=person.id"
            . " INNER JOIN dealer ON dealer.id=dlrl.dealer_id LEFT JOIN location_view lv ON lv.l5_id=dlrl.location_id  $filterstr ";
       // h1($q);
        list($opt, $rs) = run_query($dbc, $q, 'multi');
        if (!$opt)
            return $out;
        $i = 1;
        while ($row = mysqli_fetch_assoc($rs)) {
            $id = $i;
            $out[$id] = $row;
            $i++;
        }
        return $out;
    }
    
     public function get_attendance_summary_list($filter = '', $records = '', $orderby = '',$date) {
        global $dbc;
        $filterstr = $this->oo_filter($filter, $records,$orderby);
        $out = NULL;
        $q = "SELECT DISTINCT l4_id, l4_name FROM dealer "
            . "INNER JOIN dealer_location_rate_list dlrl ON dlrl.dealer_id=dealer.id "
            . "INNER JOIN person_login pl ON pl.person_id=dealer.id and pl.person_status = '1'"   
            . "LEFT JOIN location_view lv ON lv.l5_id=dlrl.location_id  $filterstr ";
      //  h1($q);
        list($opt, $rs) = run_query($dbc, $q, 'multi');
        if (!$opt)
            return $out;
        $i = 1;
        while ($row = mysqli_fetch_assoc($rs)) {
            $id = $i;
            $out[$id] = $row;
            $i++;
        }
        return $out;
    }
    public function get_attendance_summary($filter,$zone,$division,$date) {
       
        $att_date = date('Y-m-d', strtotime($date));
        $filterstr=$this->oo_filter($filter,"","");
        global $dbc;
        $out = NULL;
        $q = "SELECT COUNT(DISTINCT(uda.user_id))as tot_status FROM user_daily_attendance uda INNER JOIN person ON person.id=uda.user_id"
            . " INNER JOIN dealer_location_rate_list dlrl ON dlrl.user_id=uda.user_id"
            . " INNER JOIN dealer ON dealer.id=dlrl.dealer_id INNER JOIN location_view lv ON lv.l5_id=dlrl.location_id  $filterstr AND l4_id='$zone' AND dealer_division='$division'"
            . " AND DATE_FORMAT(`work_date`,'%Y-%m-%d') = '$att_date'";
         //h1($q);
        list($opt, $rs) = run_query($dbc, $q, 'single');
        if (!$opt)
            return $out;
        $time=$rs['tot_status'];
        return $time;
    }

public function get_last_time($user, $date) {
        $date = date('Y-m-d', strtotime($date));
        global $dbc;
        $out = NULL;
        $q = "SELECT MAX(time)as etime FROM user_sales_order where user_id= $user AND date = '$date'";
        // h1($q);
        list($opt, $rs) = run_query($dbc, $q, 'single');
        if (!$opt)
            return $out;
        $time=$rs['etime'];
        return $time;
    }
    
     public function get_sale_details($user, $date) {
        $date = date('Y-m-d', strtotime($date));
        global $dbc;
        $out = NULL;
        $q = "SELECT MIN(time)as stime FROM user_sales_order where user_id= $user AND date = '$date'";
        list($opt, $rs) = run_query($dbc, $q, 'single');
        if (!$opt)
            return $out;
        return $rs['stime'];
    }
    
     public function get_sale_time_list($filter = '', $records = '', $orderby = '') {
        global $dbc;
        $filterstr = $this->oo_filter($filter, $records);
        $out = NULL;
        $q = "SELECT  DISTINCT(user_sales_order.user_id) as user_id, CONCAT_WS(' ',first_name,last_name) as name,
            lv.l3_name as city ,lv.l4_name as zone, lv.l4_id as zoneid, lv.l5_name as beat
            FROM person 
            INNER JOIN user_sales_order ON person.id = user_sales_order.user_id
            INNER JOIN location_view lv ON lv.l5_id=user_sales_order.location_id 
            INNER JOIN person_login ON person.id=person_login.person_id AND person_login.person_status='1'
            $filterstr group by person.id order by zoneid ASC,name ASC";
       // h1($q);
        list($opt, $rs) = run_query($dbc, $q, 'multi');
        if (!$opt)
            return $out;
        $i = 1;
        while ($row = mysqli_fetch_assoc($rs)) {
            $id = $i;
            $out[$id] = $row;
            $i++;
        }
        return $out;
    }
    
    /* public function get_user_complaint_list($filter = '', $records = '', $orderby = '') {
        //echo "manisha"; die;
        global $dbc;
        $out = array();
        // if user has send some filter use them.
        $filterstr = $this->oo_filter($filter, $records, $orderby);

        $q = "SELECT *,user_complaint.id AS id,l4_id, DATE_FORMAT(user_complaint.date_time,'%e/%b/%Y') AS wdate,DATE_FORMAT(user_complaint.date_time,'%Y%m%d') AS wdatess,DATE_FORMAT(user_complaint.date_time,'%h:%i:%s') AS wtime,CONCAT_WS(' ',first_name,middle_name,last_name) AS name "
                . " FROM user_complaint INNER JOIN person ON person.id = user_complaint.person_id"
                . " INNER JOIN dealer_location_rate_list dlrl ON dlrl.dealer_id=user_complaint.person_id "
                . "INNER JOIN dealer ON dealer.id=dlrl.dealer_id "
                . " INNER JOIN location_view lv ON lv.l5_id=dlrl.location_id $filterstr  
                    GROUP BY user_complaint.date_time
                    ORDER BY user_complaint.date_time ASC";
        h1($q);
        list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = '');
        if (!$opt)
            return $out; // if no order placed send blank array
        while ($row = mysqli_fetch_assoc($rs)) {
            $id = $row['id'];
            $out[$id] = $row;
            //$out[$id]['checkout'] = $this->get_chkout_address($row['user_id'],$row['wdatess']);
        }
        //pre($out);
        return $out;
    }*/
    
    
     public function get_type($type) {
        global $dbc;
        $out = NULL;
        $q = "SELECT name FROM complaint_type WHERE id = $type";
        list($opt, $rs) = run_query($dbc, $q, $mode = 'single', $msg = '');
        if (!$opt)
            return $out;
        return $rs['name'];
    }
    
     public function get_user_complaint_list($filter = '', $records = '', $orderby = '') {
        //echo "manisha"; die;
       global $dbc;
        $filterstr = $this->oo_filter($filter, $records);
        
		$out = array();
                
                $filterstr=$this->oo_filter($filter, $records, $orderby);
                $mtype = $_SESSION[SESS.'constant']['retailer_level'];
		$q = "SELECT *,CONCAT_WS(' ',first_name,last_name) as name FROM `complaint` "
                        . "INNER JOIN person on person.id=complaint.user_id "
                        . " INNER JOIN dealer_location_rate_list dlrl on dlrl.user_id=complaint.user_id "               
                           . " LEFT JOIN location_view lv ON lv.l5_id=dlrl.location_id "
                        . "$filterstr";
                
                
                  
               // h1($q);
                list($opt,$rs)= run_query($dbc,$q,$mode='multi',$msg='');
		if(!$opt) return $out;             
		while($row = mysqli_fetch_assoc($rs)){
                    
			$id = $row['id'];
			$out[$id] = $row; 
                        $out[$id]['user_name'] = $this->get_username($row['user_id']);
                       
                        $out[$id]['type'] = $this->get_type($row['complaint_type']);
                      //   echo "manisha";
                        $cid = $row['complaint_id'];
                        $out[$id]['msg'] = $this->get_my_reference_array_direct("SELECT *, date as cdate FROM `complaint_history` WHERE complaint_history.complaint_id = $cid", 'id');  
                   
		}// while($row = mysqli_fetch_assoc($rs)){ ends
           //  pre($out);
		return $out;
    }
    
     public function get_notification_report_list($filter = '', $records = '', $orderby = '') {
        global $dbc;
        $out = array();
        // if user has send some filter use them.
        $filterstr = $this->oo_filter($filter, $records, $orderby);

        //$q = "SELECT *,ws.name as w_name ,user_daily_attendance.id AS uid,DATE_FORMAT(work_date,'%e/%b/%Y') AS wdate,DATE_FORMAT(work_date,'%Y%m%d') AS wdatess, DATE_FORMAT(work_date,'%h:%i:%s') AS wtime,CONCAT_WS(' ',first_name,middle_name,last_name) AS name,rolename FROM user_daily_attendance LEFT JOIN user_dealer_retailer USING(user_id) LEFT JOIN dealer_location_rate_list USING(dealer_id) LEFT JOIN person ON person.id = user_dealer_retailer.user_id LEFT JOIN _role USING(role_id) INNER JOIN _working_status ws ON user_daily_attendance.work_status = ws.id $filterstr ";
        $q = "SELECT *,sale_reason_remarks.id AS id,l4_id,CONCAT_WS(' ',person.first_name,person.last_name) as pname,retailer.name AS rname FROM sale_reason_remarks"
                . " INNER JOIN person ON person.id = sale_reason_remarks.user_id "
                . " INNER JOIN retailer on retailer.id=sale_reason_remarks.retailer_id"
                . " INNER JOIN dealer_location_rate_list dlrl on dlrl.user_id=sale_reason_remarks.user_id "               
                . " LEFT JOIN location_view lv ON lv.l5_id=dlrl.location_id $filterstr";
        
//        $q="SELECT sale_remarks,date,time,lv.state_id,sale_reason_remarks.id AS id,l4_id,CONCAT_WS(' ',person.first_name,person.last_name) as pname,retailer.name AS rname FROM sale_reason_remarks 
//            INNER JOIN person ON person.id = sale_reason_remarks.user_id 
//            INNER JOIN retailer on retailer.id=sale_reason_remarks.retailer_id
//            INNER JOIN location_view lv ON lv.state_id=person.state_id $filterstr";
      //  h1($q);
        $rs = mysqli_query($dbc,$q);
       // list($opt, $rs) = run_query($dbc, $q, $mode = 'multi');
        //if (!$opt)
        //    return $out; // if no order placed send blank array
        while ($row = mysqli_fetch_assoc($rs)) {
            $id = $row['id'];
            $out[$id] = $row;
            //$out[$id]['checkout'] = $this->get_chkout_address($row['user_id'],$row['wdatess']);
        }
        // pre($out);
        return $out;
    }
    
     public function get_user_daily_sales_list($filter = '', $records = '', $orderby = '') {
        //$q = "SELECT *,user_sales_order_details.id as unq_id,(user_sales_order_details.rate * user_sales_order_details.quantity)as sale_val,  dealer.name as d_name, retailer.name as rt_name, catalog_product.name as pt_name From user_sales_order_details INNER JOIN user_sales_order USING (order_id) INNER JOIN dealer ON dealer.id = user_sales_order.dealer_id INNER JOIN retailer ON retailer.id = user_sales_order.retailer_id
        //INNER JOIN catalog_product ON user_sales_order_details.product_id=catalog_product.id  $filterstr";
        global $dbc;
        $out = array();
        $filterstr = $this->oo_filter($filter, $records, $orderby);
        //print_r($filterstr);
        $mtype = $_SESSION[SESS . 'constant']['retailer_level'];
        // h1($mtype);
        $q = "SELECT retailer.name as retailer_name,l5_name as r_location ,user_sales_order.user_id as user_id,
            l3_name AS city,user_sales_order.call_status,
            DATE_FORMAT(date,'%d/%m/%Y') AS sale_date,DATE_FORMAT(date,'%d/%b/%Y') AS fdated,
            user_sales_order.retailer_id,user_sales_order.dealer_id,user_sales_order.order_id,
            lv.l3_name AS city,lv.l4_name AS zone,lv.l4_id AS zoneid,lv.l5_name AS beat
            FROM user_sales_order 
            INNER JOIN  person on person.id=user_sales_order.user_id
            INNER JOIN retailer ON user_sales_order.retailer_id = retailer.id 
            INNER JOIN person_login ON person.id=person_login.person_id AND person_login.person_status='1'
            LEFT JOIN dealer_location_rate_list dlrl ON dlrl.user_id=user_sales_order.dealer_id INNER JOIN dealer ON dealer.id=dlrl.dealer_id
            LEFT JOIN location_view lv ON lv.l5_id=dlrl.location_id $filterstr GROUP BY user_sales_order.order_id 
            ORDER BY sale_date ASC,zoneid ASC,beat ASC,person.first_name ASC";
        // h1($q);
      
         $rs=mysqli_query($dbc,$q);   
         $dealer_map = get_my_reference_array('dealer', 'id', 'name');       
        while ($row = mysqli_fetch_assoc($rs)) {
           // print_r($row);
            $id = $row['order_id'];
            $out[$id] = $row; // storing the item id
            $out[$id]['d_name'] = $dealer_map[$row['dealer_id']];
            $out[$id]['firm_name'] = $row['retailer_name'];
            $out[$id]['person_name'] = $this->get_username($row['user_id']);
            $out[$id]['order_item'] = $this->get_my_reference_array_direct("
                SELECT usod.id,cp.name,usod.rate,usod.quantity,usod.scheme_qty 
                FROM user_sales_order_details usod INNER JOIN user_sales_order uso ON usod.order_id = uso.order_id 
                INNER JOIN catalog_product cp ON usod.product_id=cp.id WHERE usod.order_id = $row[order_id]", 'id');
        }// while($row = mysqli_fetch_assoc($rs)){ ends
      //   pre($out);exit;
        return $out;
    }
    
     ################################ sales man secondary###############################################



 public function get_sales_man_sale_list($filter = '', $records = '', $orderby = '',$date) {
        global $dbc;
        $filterstr = $this->oo_filter($filter, $records,$orderby);
        $out = NULL;
       /* $q = "SELECT distinct dealer.name as dealer_name,dealer_id,rolename,l4_id as zone_id,person.id as user_id,"
                . "CONCAT_WS(' ',first_name,last_name) as pname FROM dealer_location_rate_list dlrl INNER "
                . "JOIN person ON person.id=dlrl.dealer_id INNER JOIN _role USING(role_id) "
                . "INNER JOIN person_login pl ON pl.person_id=person.id "
                . "INNER JOIN dealer ON dealer.id=dlrl.dealer_id "
                . "LEFT JOIN location_view lv ON lv.l5_id=dlrl.location_id $filterstr ";*/
        
        $q = "SELECT uso.amount AS total,uso.date,dealer.name as dealer_name,dlrl.dealer_id,rolename,l4_name as zone,l4_id as zone_id,uso.user_id,CONCAT_WS(' ',first_name,last_name) as pname FROM dealer_location_rate_list dlrl"
            . " INNER JOIN person ON person.id=dlrl.user_id INNER JOIN _role USING(role_id) "           
            . " INNER JOIN dealer ON dealer.id=dlrl.dealer_id INNER JOIN location_view lv ON lv.l5_id=dlrl.location_id INNER JOIN user_sales_order AS uso ON uso.user_id=person.id $filterstr ";
        // h1($q);
        list($opt, $rs) = run_query($dbc, $q, 'multi');
        if (!$opt)
            return $out;
        $i = 1;
        while ($row = mysqli_fetch_assoc($rs)) {
            $id = $i;
            $out[$id] = $row;
            $i++;
        }
        return $out;
    }
    

public function get_retailer_count($user,$beat) {
global $dbc;
$out = NULL;

$q = "SELECT count(retailer.id) AS ret FROM `retailer` INNER JOIN user_dealer_retailer ON retailer.id=user_dealer_retailer.retailer_id WHERE user_id = '$user' AND retailer.location_id='$beat'";
// h1($q);
list($opt, $rs) = run_query($dbc, $q, 'multi');
if (!$opt)
return $out;
while ($rows = mysqli_fetch_array($rs)) {
$ret=$rows[ret];
}
$temp = array();
$temp[0] = $ret;
// pre($temp);
return $temp;
}


    
################################ sales man secondary###############################################





    
     public function get_dealer_ss_list($filter = '', $records = '', $orderby = '',$date) {

        global $dbc;
        // pre($date);
        $filterstr = $this->oo_filter($filter, $records, $orderby);
        $start_date=$date[0];
        $end_date=$date[1];
        $inc = 1;
        $out = array();
        //$q = "SELECT *,DATE_FORMAT(date,'%d-%m-%Y')as date,dealer.id as did,uso.order_id as order_id,depot.name as dname,ss_report.name as sname,dealer.name as dename FROM depot INNER JOIN ss_report ON depot.depo_id=ss_report.depo_id INNER JOIN dealer ON "
        //        . " dealer.ss_id=ss_report.id INNER JOIN user_sales_order uso ON uso.dealer_id=dealer.id $filterstr GROUP BY uso.order_id ASC ORDER BY uso.date ASC";

        $q="SELECT sum(rate*quantity) as total,
            DATE_FORMAT(date,'%d-%m-%Y')as `date`,
            depot.name as dname,
            ss_report.name as sname,
            dealer.name as dename,
            dealer.id as did
            FROM depot
            INNER JOIN ss_report ON depot.depo_id=ss_report.depo_id
            INNER JOIN dealer ON dealer.ss_id=ss_report.id
            INNER JOIN user_sales_order uso ON uso.dealer_id=dealer.id
            INNER JOIN user_sales_order_details usod ON uso.order_id=usod.order_id
            $filterstr GROUP BY uso.dealer_id ORDER BY uso.date ASC,dealer.name ASC";
        // h1($q);
        list($opt,$rs) = run_query($dbc, $q, $mode = 'multi', $msg = '');
        if (!$opt)
            return $out;
        while ($row = mysqli_fetch_assoc($rs)) {
            $id = $row['did'] . $inc;
            $out[$id]['depot_name'] = $row['dname'];
            $out[$id]['date'] = $row['date'];
            $out[$id]['ss_name'] = $row['sname'];
            $out[$id]['dealer_name'] = $row['dename'];
            $out[$id]['tsv'] = $row['total'];
            $out[$id]['counter_sale'] =$this->get_counter_sale($row['did'],$start_date,$end_date);
            $inc++;
        }

        return $out;
    }
    
    
     public function get_effective_coverage_area_detail($date_filter, $user_id, $location_id, $dealer_id) {
        global $dbc;
        $out = NULL;
        $output = array();
        if (!empty($date_filter)) {
            //$date_filter_str = implode(' AND ', $date_filter);
            $filter[] = "date='$date_filter'";
        }
        if (!empty($user_id)) {

            $filter[] = "user_id = '$user_id'";
        }
        if (!empty($location_id)) {

            $filter[] = "location_id = '$location_id'";
        }
        if (!empty($dealer_id)) {

            $filter[] = "dealer_id = '$dealer_id'";
        }
        $filterstr = $this->oo_filter($filter, $records = '', $orderby = '');
        $q = "SELECT retailer_id FROM user_sales_order $filterstr";
        // h1($q);
        list($opt, $rs) = run_query($dbc, $q, 'multi');
        if (!$opt)
            return $out;
        while ($row = mysqli_fetch_array($rs)) {
            $output[] = $row['retailer_id'];
        }
        return $output;
    }
    
    public function get_effective_coverage_area($date_filter, $user_id, $location_id, $dealer_id) {
        global $dbc;
        $out = NULL;
        if (!empty($date_filter)) {
            //  $date_filter_str = implode(' AND ', $date_filter);
            $filter[] ="date='$date_filter'";
        }
        if (!empty($user_id)) {

            $filter[] = "user_id = '$user_id'";
        }
        if (!empty($location_id)) {

            $filter[] = "location_id = '$location_id'";
        }
        if (!empty($dealer_id)) {

            $filter[] = "dealer_id = '$dealer_id'";
        }
        $filterstr = $this->oo_filter($filter, $records = '', $orderby = '');
        $q = "SELECT COUNT(DISTINCT(retailer_id)) AS total FROM user_sales_order $filterstr";
        //h1($q);
        list($opt, $rs) = run_query($dbc, $q, 'single');
        if (!$opt)
            return $out;
        else
            return $rs['total'];
    }
    
    public function get_total_coverage_area($location_id) {
        global $dbc;
        $out = NULL;
        $q = "SELECT COUNT(id) AS total FROM retailer WHERE location_id IN ($location_id)";
        // h1($q);
        list($opt, $rs) = run_query($dbc, $q, 'single');
        if (!$opt)
            return $out;
        return $rs['total'];
    }
    
    public function get_total_lpc($user_id, $location_id, $date_filter, $dealer_id) {
        global $dbc;
        $out = NULL;
        if (!empty($date_filter)) {
            // $date_filter_str = implode(' AND ', $date_filter);
            $filter[] ="date='$date_filter'";
        }
        if (!empty($user_id)) {

            $filter[] = "user_id = '$user_id'";
        }
        if (!empty($location_id)) {

            $filter[] = "location_id = '$location_id'";
        }
        if (!empty($dealer_id)) {

            $filter[] = "dealer_id = '$dealer_id'";
        }
        $filterstr = $this->oo_filter($filter, $records = '', $orderby = '');

        $q = "SELECT COUNT(DISTINCT(product_id)) AS count FROM user_sales_order_details INNER JOIN user_sales_order USING(order_id) $filterstr";
        // h1($q);
        list($opt, $rs) = run_query($dbc, $q, 'single');
        if (!$opt)
            return $out;
        else
            return $rs['count'];
    }
    
    public function get_total_sale($user, $date, $dealer) {
        global $dbc;
        $out = NULL;

        $q = "SELECT SUM(rate * quantity) AS total FROM user_sales_order_details INNER JOIN user_sales_order USING(order_id) WHERE user_id = $user AND dealer_id = $dealer AND  `date` = '$date' GROUP BY date ";
        //  h1($q);
        list($opt, $rs) = run_query($dbc, $q, 'single');
        if (!$opt)
            return $out;
        return $rs['total'];
    }
    
      public function get_call_detils($user, $location, $date, $dealer) {
        global $dbc;
        $out = NULL;

        $q = "SELECT call_status FROM user_sales_order WHERE user_id = '$user' AND dealer_id = '$dealer' AND date='$date' ";
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
            else {
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
    
    
     public function get_total_coverage_area_deatils($location_id) {
        global $dbc;
        //$out = NULL;
        $retailer = array();
        $q = "SELECT id  FROM retailer WHERE location_id IN ($location_id) AND retailer_status=1";
        $res = mysqli_query($dbc, $q);
        while ($row = mysqli_fetch_array($res)) {
            $retailer[] = $row['id'];
        }
        return $retailer;
    }
    
    public function get_call_details($user, $location, $date, $dealer) {
        global $dbc;
        $out = NULL;

        $q = "SELECT call_status FROM user_sales_order WHERE user_id = '$user' AND dealer_id = '$dealer' AND date='$date' AND location_id = '$location'";
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
      //    pre($temp);
        return $temp;
    }
    


    
     public function get_advance_summary_report_list($filter = '', $records = '', $orderby = '', $date_filter = '') {
        $holder = array();
        global $dbc;
        $out = array();
        $filterstr = $this->oo_filter($filter, $records, $orderby);
        $location_level = $_SESSION[SESS . 'constant']['location_level'];
        $q="SELECT user_sales_order.user_id, _role.rolename,emp_code AS ecod,
            l5_name AS name5,
            l5_id AS location_5_id,
            SUM(rate * quantity)as total_sale,
            l4_name as name4,
            l4_id as zoneid,
            l2_name as name2,
            DATE_FORMAT(date,'%d-%m-%Y') as dates,
            DATE_FORMAT(date,'%Y-%m-%d') as `date`,
            user_sales_order.id AS uniq, 
            user_sales_order.dealer_id AS dealer_id,person_id_senior,
            CONCAT_WS(' ',first_name, middle_name, last_name) AS person_name,
            dealer.name AS dealer_name,user_sales_order.location_id AS usolid,
            user_sales_order.dealer_id AS did
            FROM user_sales_order INNER JOIN user_sales_order_details USING(order_id)
            INNER JOIN person ON person.id = user_sales_order.user_id 
            INNER JOIN _role USING(role_id) 
            INNER JOIN dealer ON dealer.id = user_sales_order.dealer_id
            LEFT JOIN retailer ON user_sales_order.retailer_id = retailer.id             
            LEFT JOIN location_view lv ON lv.l5_id=user_sales_order.location_id  $filterstr  GROUP BY user_sales_order.`date`,user_sales_order.user_id 
                ORDER BY user_sales_order.`date` ASC,zoneid ASC,l5_name ASC,person_name ASC" ;
      // h1($q);
        list($opt, $rs) = run_query($dbc, $q, 'multi');
        if (!$opt)
            return $out;

        while ($row = mysqli_fetch_assoc($rs)) {
            $id = $row['uniq'];
           
            $out[$id] = $row;
          
            $out[$id]['senior_person_name'] = myrowval($table = "person", $col = "CONCAT_WS(' ', first_name,middle_name,last_name)", $where = "id = '$row[person_id_senior]'");
          //  echo "aaaaaaaaaaa";
            $out[$id]['effetive_coverage_area'] = $this->get_effective_coverage_area($row['date'], $row['user_id'], $row['usolid'], $row['did']);
        //  echo "bbbbbbbbbbb";
            $out[$id]['total_coverage_area'] = $this->get_total_coverage_area($row['usolid']);
        //   echo "ccccccccccccc";
            $out[$id]['lpc'] = $this->get_total_lpc($row['user_id'], $row['usolid'], $row['date'], $row['did']);
      //   echo "ddddddddddd";
            $out[$id]['total_sale'] = $this->get_total_sale($row['user_id'],$row['date'], $row['did']);
       // echo "eeeeeeeeeeeeee";
            $holder = $this->get_call_details($row['user_id'], $row['usolid'], $row['date'], $row['did']);
      //  echo "ffffffffffff";
            $out[$id]['total_call'] = $holder[0];
            $out[$id]['productive'] = $holder[1];
            $out[$id]['non_productive'] = $holder[2];
        }
        //  pre($out);
        return $out;
    }
    public function get_user_count_list($filter = '', $records = '', $orderby = '', $date_filter = '') {
        $holder = array();
        global $dbc;
        $out = array();
        $filterstr = $this->oo_filter($filter, $records, $orderby);
        $location_level = $_SESSION[SESS . 'constant']['location_level'];
        $q="SELECT lv.l2_name AS state,count(person.id) AS login_created_count
            
            FROM person INNER JOIN person_login pl ON pl.person_id=person.id INNER JOIN location_view lv ON lv.l2_id=person.state_id WHERE person_status='1' group by state" ;
      //h1($q);
        list($opt, $rs) = run_query($dbc, $q, 'multi');
        if (!$opt)
            return $out;

        while ($row = mysqli_fetch_assoc($rs)) {
            $id = $row['uniq'];
           
            $out[$id] = $row;


          
          //  $out[$id]['senior_person_name'] = myrowval($table = "person", $col = "CONCAT_WS(' ',first_name,middle_name,last_name)", $where = "id = '$row[person_id_senior]'");
          //  echo "aaaaaaaaaaa";
          //  $out[$id]['effetive_coverage_area'] = $this->get_effective_coverage_area($row['date'], $row['user_id'], $row['usolid'], $row['did']);
        //  echo "bbbbbbbbbbb";
         //   $out[$id]['total_coverage_area'] = $this->get_total_coverage_area($row['usolid']);
        //   echo "ccccccccccccc";
         //   $out[$id]['lpc'] = $this->get_total_lpc($row['user_id'], $row['usolid'], $row['date'], $row['did']);
      //   echo "ddddddddddd";
          //  $out[$id]['total_sale'] = $this->get_total_sale($row['user_id'],$row['date'], $row['did']);
       // echo "eeeeeeeeeeeeee";
           // $holder = $this->get_call_details($row['user_id'], $row['usolid'], $row['date'], $row['did']);
      //  echo "ffffffffffff";
          //  $out[$id]['total_call'] = $holder[0];
           // $out[$id]['productive'] = $holder[1];
           // $out[$id]['non_productive'] = $holder[2];
        }
        //  pre($out);
        return $out;
    }






    
     public function get_secondry_new_sale($user, $date, $dealer,$retailer) {
        global $dbc;
        $out = NULL;
//        if (!empty($date)) {
//            $date_filter_str = implode(' AND ', $date);
//            $filter[] = $date_filter_str;
//        }
        $q = "SELECT SUM(rate * quantity) AS total FROM user_sales_order_details 
            INNER JOIN user_sales_order USING(order_id) WHERE user_id = '$user' 
                AND dealer_id = '$dealer' AND user_sales_order.`date`='$date'  
                     AND retailer_id='$retailer'";
        // h1($q);
        list($opt, $rs) = run_query($dbc, $q, 'single');
        if (!$opt)
            return $out;
        $total = my2digit($rs['total']);
        return $total;
    }
    
    public function get_rds_report_list($filter = '', $records = '', $orderby = '', $date_filter = '') {
        global $dbc;
        $out = array();
        $filterstr = $this->oo_filter($filter, $records, $orderby);

        $q = "SELECT DATE_FORMAT(`date`,'%Y-%m-%d') as `date`,DATE_FORMAT(`date`,'%d-%m-%Y') as `show_date`,
            user_sales_order.id AS uniq,lv.l3_name as city,l4_id,
            CONCAT_WS(' ',first_name, middle_name, last_name) AS person_name,user_sales_order.user_id as user_id,
            dealer.name AS dealer_name,person.person_id_senior,retailer.name as retailer_name,
            user_sales_order.location_id AS usolid,
            user_sales_order.dealer_id AS did,
            user_sales_order.retailer_id
            FROM user_sales_order
            INNER JOIN person ON person.id = user_sales_order.user_id
            INNER JOIN _role USING(role_id)
            INNER JOIN dealer ON dealer.id = user_sales_order.dealer_id
            INNER JOIN retailer ON user_sales_order.retailer_id = retailer.id
            INNER JOIN location_view lv ON lv.l5_id = user_sales_order.location_id  $filterstr
            GROUP BY user_sales_order.date,user_sales_order.user_id,user_sales_order.retailer_id 
            ORDER BY user_sales_order.`date` ASC,l4_id ASC,retailer.name ASC";

       // h1($q);
        list($opt, $rs) = run_query($dbc, $q, 'multi');
        if (!$opt)
            return $out;

        while ($row = mysqli_fetch_assoc($rs)) {
            $id = $row['uniq'];
            //  $id = $row['user_id'].$row['usolid'].$row['did'];
            $out[$id] = $row;
            //myrowval($table="person", $col="CONCAT_WS(' ', first_name,middle_name,last5_name) AS name", $where="person_id_senior = '$row['person_id_senior']'")
            $out[$id]['senior_person_name'] = myrowval($table = "person", $col = "CONCAT_WS(' ', first_name,middle_name,last_name)", $where = "id = '$row[person_id_senior]'");
            //$out[$id]['retailer_name']=  myrowval('retailer','name',"id=$row[retailer_id]");
            //$out[$id]['effetive_coverage_area'] = $this->get_effective_coverage_area($date_filter, $row['user_id'], $row['usolid'], $row['did']);
            $out[$id]['secondry_sale'] = $this->get_secondry_new_sale($row['user_id'], $row['date'], $row['did'],$row['retailer_id']);
        }
        //pre($out);
        return $out;
    }
    
    public function get_state($id) {
        global $dbc;
        $out = NULL;
        $q = "SELECT l1_name as state FROM person INNER JOIN dealer_location_rate_list dlrl ON dlrl.user_id = person.id "
            . " INNER JOIN location_view ON location_view.l5_id=dlrl.location_id WHERE user_id = $id LIMIT 0,1";

        list($opt, $rs) = run_query($dbc, $q, $mode = 'single', $msg = '');
        if (!$opt)
            return $out;
        return $rs['state'];
    }
    
     public function get_market($id,$date) {
        global $dbc;
        $out = NULL;
        $q = "SELECT l5.name as market from user_sales_order uso LEFT JOIN location_5 l5 ON l5.id=uso.location_id WHERE user_id = $id AND date='$date' LIMIT 0,1";
        list($opt, $rs) = run_query($dbc, $q, $mode = 'single', $msg = '');
        if (!$opt)
            return $out;
        return $rs['market'];
    }
    
      public function get_my_reference_value_direct($q, $primarykey,$col)
	{
		global $dbc;
		$out = array();
		list($opt, $rs) = run_query($dbc, $q, 'multi');
		if(!$opt) return $out;
		$row = mysqli_fetch_assoc($rs);                
		return $row[$col];
	}
    
    ################################ dsr report###############################################



 public function get_dsr_month_wise_list($filter = '', $records = '', $orderby = '', $datearray) {
        global $dbc;
        $out = array();
        $inc = 1;
        $filterstr = $this->oo_filter($filter, $records, $orderby);

        $mtype = $_SESSION[SESS . 'constant']['retailer_level'];
        //pre($datearray);
        foreach ($datearray as $key => $value) {
            //foreach ($user as $k => $val) {

            //$id = $row['order_id'];
            $id = $inc;
            $q = "select distinct(user_id) as user_id from user_sales_order 
                  INNER JOIN dealer ON dealer.id=user_sales_order.dealer_id
                  INNER JOIN location_view on user_sales_order.location_id=l5_id
                  INNER JOIN person p ON p.id=user_sales_order.user_id 
                  $filterstr AND date='$value'";
            //h1($q);
            $u_res = mysqli_query($dbc, $q);
            while($uso_res=mysqli_fetch_assoc($u_res)){
                $k=$uso_res['user_id'];
                $val=$uso_res['user_id'];
                if(mysqli_num_rows($u_res)>0){

                    $out[$value][$k]['state'] = $this->get_state($k);
                  // echo "aaaaaaa";
                    $out[$value][$k]['person_name'] = $this->get_username($k);
                  // echo "bbbbbbbb"; 
                    $out[$value][$k]['market'] = $this->get_market($k,$value);
                   // echo "cccccccccc"; 
                    $q="SELECT count(distinct(retailer_id))as count from user_sales_order where date = '$value' AND user_id = '$val'";
                    $out[$value][$k]['tc'] = $this->get_my_reference_value_direct($q, 'id','count');
                  //echo "dddddddddddd"; 
                    $q = "SELECT count(distinct(retailer_id))as count from user_sales_order where date = '$value' AND user_id = '$val' AND call_status = '1' ";
                    $out[$value][$k]['pc'] = $this->get_my_reference_value_direct($q, 'id','count');
                  // echo "eeeeeeeeee";  
                    $out[$value][$k]['pc_per'] = my2digit(($out[$value][$k]['pc']*100)/$out[$value][$k]['tc']).'%';
                 //  echo "ffffffffff"; 
                    $q="SELECT product_id,SUM(quantity) as q from user_sales_order INNER JOIN user_sales_order_details USING(order_id) where date = '$value' AND user_id = '$val' GROUP BY product_id";
                    $out[$value][$k]['sku'] = $this->get_my_reference_array_direct($q, 'product_id');
                 //  echo "ggggggggggg"; 
                    $q="SELECT SUM(rate * quantity)as count from user_sales_order INNER JOIN user_sales_order_details USING(order_id) where date = '$value' AND user_id = '$val'";
                    $out[$value][$k]['sale'] = my2digit($this->get_my_reference_value_direct($q, 'id','count'));

                }else{

                    $out[$value][$k]['state'] = $this->get_state($k);
                    $out[$value][$k]['person_name'] = $this->get_username($k);
                    $out[$value][$k]['market'] = '-';
                    $out[$value][$k]['tc'] = 0;
                    $out[$value][$k]['pc'] = 0;
                    $out[$value][$k]['pc_per'] = '0%';
                    $out[$value][$k]['sku'] = 0;
                    $out[$value][$k]['sale'] = "0.00";

                }

                $inc++ ;
            }
        }
      //  pre($out); exit;
        return $out;
    }
   

################################ dsr report###############################################
    
     public function get_tcpc_by_orderids($user_id,$cdate) {
        global $dbc;
        $out = array();
        $q="select count(uso.order_id) as prod_call FROM user_sales_order uso where uso.user_id='$user_id' AND $cdate AND uso.call_status='1'";
//h1($q);
          list($opt, $rs) = run_query($dbc, $q, 'single');
        if (!$opt)
            return $out;
        return $rs['prod_call'];
    }

    
    ################################ Classification wise report###############################################


 public function get_clasification_wise_sale_list($filter = '', $records = '', $orderby = '',$cdate = '',$division ='') {
        global $dbc;
        $out = array();
        $filterstr = $this->oo_filter($filter, $records, $orderby);
        //pre($filterstr);

        $q="SELECT uso.id as uniq,
              CONCAT_WS(' ',p.first_name, p.middle_name, p.last_name) AS person_fullname,state.statename AS State,uso.user_id,
              group_concat(distinct order_id) as order_ids
            FROM user_sales_order uso           
            INNER JOIN person p ON uso.user_id=p.id
            INNER JOIN person_login ON p.id=person_login.person_id AND person_login.person_status='1'
            INNER JOIN state ON state.stateid=p.state_id
            $filterstr AND role_id in (3,10,14,15,21) AND call_status='1' GROUP BY uso.user_id";

   //h1($q);
        list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = '');
        if (!$opt)
            return $out;
        while ($row = mysqli_fetch_assoc($rs)) {
            $id = $row['uniq'];         
            $out[$id]['person_fullname'] = $row['person_fullname']; 
            $out[$id]['State'] = $row['State'];               
            $out[$id]['total_retailers'] = $this->get_total_retailer_area($row['user_id']);            
            $out[$id]['tcpc'] = $this->get_tcpc_by_orderids($row['user_id'],$cdate);            
            $out[$id]['sale_value'] = $this->get_classification_total_by_orderids($cdate,$row['user_id'],$division);
            $out[$id]['class_retailer'] = $this->get_classification_total_retailer($cdate,$row['user_id'],$division);
        }
        return $out;
    }
    
    public function get_total_retailer_area($user_id) {
        global $dbc;
        $out = NULL;
        $q = "SELECT COUNT(id) AS total FROM retailer INNER JOIN user_dealer_retailer udr ON udr.retailer_id=retailer.id WHERE udr.user_id='$user_id' GROUP BY udr.user_id";
        // h1($q);
        list($opt, $rs) = run_query($dbc, $q, 'single');
        if (!$opt)
            return $out;
        return $rs['total'];
    }
    
      public function get_classification_total_by_orderids($cdate,$user_id,$division) {
        global $dbc;
        $out = array();//            
   
           $qc= "select cv.`c1_name` as class,SUM(rate*quantity) as total from user_sales_order_details usod "
               . "INNER JOIN user_sales_order uso USING(order_id) INNER JOIN catalog_view cv ON cv.product_id=usod.product_id "            
               . "WHERE user_id='$user_id' AND $cdate AND $division GROUP BY class";           
       //h1($qc);
        list($opt, $rs) = run_query($dbc, $qc, $mode = 'multi', $msg = '');
        if (!$opt)
            //pre($out);
            return $out;
        while ($row = mysqli_fetch_assoc($rs)) {          
            $out[$row['class']] = $row['total'];           
        }
        
        return $out;
    }
    
      public function get_classification_total_retailer($cdate,$user_id,$division) {
        global $dbc;
        $out = array();//    
     
           $qc= "select cv.`c1_name` as class,count(distinct retailer_id) as retailer_count from user_sales_order_details usod "
               . "INNER JOIN user_sales_order uso USING(order_id) INNER JOIN catalog_view cv ON cv.product_id=usod.product_id "            
               . "WHERE user_id='$user_id' AND $cdate AND $division GROUP BY class";
           
       // h1($qc);
        list($opt, $rs) = run_query($dbc, $qc, $mode = 'multi', $msg = '');
        if (!$opt)
            //pre($out);
            return $out;
        while ($row = mysqli_fetch_assoc($rs)) {
          
            $out[$row['class']] = $row['retailer_count'];           
        }
        
        return $out;
    }
    
    public function get_detail_report_advanced_list($filter = '', $records = '', $orderby = '',$date_filter) {
        $holder = array();
        global $dbc;
        $out = array();
        $filterstr = $this->oo_filter($filter, $records, $orderby);
        // $date_filterstr = $this->oo_filter($date_filter, $records='');

        //    $lpc_filterstr = $this->oo_filter($lpc_filter, $records = '', $orderby = '');
        $location_level = $_SESSION[SESS . 'constant']['location_level'];
        //h1($location_level);
        //$dynamic_loc = $location_level - 1 ;

        $q = "SELECT user_sales_order.user_id, _role.rolename,
            sum(user_sales_order.total_sale_value) as total_sale,
            l5_name AS name5,
            l5_id AS location_5_id,
            l4_name as name4,
            l4_id as id4,l3_name as name3,
            l2_name as name2,
            DATE_FORMAT(`date`,'%d-%b-%Y') as dates,DATE_FORMAT(`date`,'%Y-%m-%d') as `date`,user_sales_order.id AS uniq, 
            user_sales_order.dealer_id AS dealer_id,person_id_senior,
            CONCAT_WS(' ',first_name, middle_name, last_name) AS person_name,
            dealer.name AS dealer_name,user_sales_order.location_id AS usolid,
            user_sales_order.dealer_id AS did
            FROM user_sales_order 
            INNER JOIN person ON person.id = user_sales_order.user_id 
            INNER JOIN _role USING(role_id) 
            INNER JOIN dealer ON dealer.id = user_sales_order.dealer_id
            INNER JOIN retailer ON user_sales_order.retailer_id = retailer.id 
            INNER JOIN person_login ON person.id=person_login.person_id AND person_login.person_status='1'
            INNER JOIN location_view lv ON lv.l5_id=user_sales_order.location_id  $filterstr  GROUP BY user_sales_order.`date`,user_sales_order.user_id";
       // error_log($q);
       // h1($q);
        list($opt, $rs) = run_query($dbc, $q, 'multi');
        if (!$opt)
            return $out;
        $covered_retailers = array();
        $total_retails = array();
        $not_covered_retailers = array();
        while ($row = mysqli_fetch_assoc($rs)) {
            $id = $row['uniq']; //$row['user_id'] . $row['usolid'] . $row['did'];
            $out[$id] = $row;
            //myrowval($table="person", $col="CONCAT_WS(' ', first_name,middle_name,last5_name) AS name", $where="person_id_senior = '$row['person_id_senior']'")
            $out[$id]['senior_person_name'] = myrowval($table = "person", $col = "CONCAT_WS(' ', first_name,middle_name,last_name)", $where = "id = '$row[person_id_senior]'");
            $out[$id]['effetive_coverage_area'] = $this->get_effective_coverage_area($row['date'], $row['user_id'], $row['usolid'], $row['did']);

            $out[$id]['total_coverage_area'] = $this->get_total_coverage_area($row['usolid']);
            $out[$id]['lpc'] = $this->get_total_lpc($row['user_id'], $row['usolid'], $row['date'], $row['did']);
            $out[$id]['total_sale'] = $this->get_total_sale($row['user_id'],$row['date'], $row['did']);
            $holder = $this->get_call_detils($row['user_id'], $row['usolid'], $row['date'], $row['did']);
            //pre($holder);
            $out[$id]['total_call'] = $holder[0];
            $out[$id]['productive'] = $holder[1];
            $out[$id]['non_productive'] = $holder[2];
            $covered_retailers = $this->get_effective_coverage_area_detail($row['date'], $row['user_id'], $row['usolid'], $row['did']);
            $total_retails = $this->get_total_coverage_area_deatils($row['usolid']);
            $not_covered_retailers = array_diff($total_retails, $covered_retailers);
            //pre($not_covered_retailers);
            $not_covered_retailers_show = implode(',', $not_covered_retailers);
            $covered_retailers = implode(',', $covered_retailers);
            $out[$id]['retailer_ids'] = $covered_retailers;

            $out[$id]['not_covered'] = $not_covered_retailers_show;
            //  pre($out);
            //  break;

        }
            //pre($out);
        return $out;
    }

    
     public function get_product_name($id) {
        global $dbc;
        $q = "SELECT name FROM catalog_product WHERE id='$id'";
        //  h1($q);
        list($opt, $rs) = run_query($dbc, $q, 'multi');
        if (!$opt)
            return $out;
        $row = mysqli_fetch_array($rs);
        $name = $row['name'];

        return $name;
    }
     public function get_damage_order_report_list($filter = '', $records = '', $orderby = '', $last_level_loc = '', $lpc_filter = '', $date_filter = '') {
        global $dbc;
        $out = array();
        $filterstr = $this->oo_filter($filter, $records, $orderby);
        // $date_filterstr = $this->oo_filter($date_filter, $records='');
//                h1($filterstr);
        $lpc_filterstr = $this->oo_filter($lpc_filter, $records = '', $orderby = '');
        $location_level = $_SESSION[SESS . 'constant']['location_level'];
        //$dynamic_loc = $location_level - 1 ;
        $str = '';
        for ($k = $location_level; $k >= 1; $k--) {
            $str .= ",location_$k.name AS name$k,location_$k.id AS location_" . $k . "_id ";
        }
//
        $q = " SELECT damage_replace.id AS did,
            damage_replace.replaceid AS s_no,
            damage_replace.user_id, prod_code,
            ret_code, prod_qty, prod_value, 
            DATE_FORMAT(date_time,'%Y-%m-%d')as date_time, 
            task, 
            mrp, 
            extra_amt,
            CONCAT_WS(' ', person.first_name,person.middle_name,person.last_name) as person_fullname,
            dealer.name 
            FROM person 
            INNER JOIN damage_replace ON person.id = damage_replace.user_id 
            INNER JOIN dealer ON damage_replace.dis_code=dealer.id 
            INNER JOIN user_dealer_retailer udr on 
            udr.user_id=person.id INNER JOIN dealer_location_rate_list dlrl ON dlrl.dealer_id=udr.dealer_id 
            INNER JOIN location_view ON 
            location_view.l5_id=dlrl.location_id 
             $filterstr";


        // h1($q);
        list($opt, $rs) = run_query($dbc, $q, 'multi');
        if (!$opt)
            return $out;

        while ($row = mysqli_fetch_assoc($rs)) {
            //$id = $row['user_id'].$row['usolid'].$row['did'];
            $id = $row['did'];
            $out[$id] = $row;
            $out[$id]['date_time'] = $row['date_time'];
            $out[$id]['first_name'] = $row['first_name'];
            $out[$id]['name'] = $row['name'];
            $out[$id]['s_no'] = $row['s_no'];
            $out[$id]['ret_code'] = $row['ret_code'];
            $out[$id]['prod_qty'] = $row['prod_qty'];
            $out[$id]['prod_value'] = $row['prod_value'];
            $out[$id]['task'] = $row['task'];
            $out[$id]['mrp'] = $row['mrp'];
            $out[$id]['extra_amt'] = $row['extra_amt'];
            $out[$id]['product_name'] = $this->get_product_name($row['prod_code']);
        }
        //pre($out);
        return $out;
    }
     ################################## damage & replace ###############################################


 public function get_damage_replace_report_list($filter = '', $records = '', $orderby = '', $last_level_loc = '', $lpc_filter = '', $date_filter = '') {
        global $dbc;
        $out = array();
        $filterstr = $this->oo_filter($filter, $records, $orderby);
        // $date_filterstr = $this->oo_filter($date_filter, $records='');
//                h1($filterstr);
        $lpc_filterstr = $this->oo_filter($lpc_filter, $records = '', $orderby = '');
        $location_level = $_SESSION[SESS . 'constant']['location_level'];
        //$dynamic_loc = $location_level - 1 ;
        $str = '';
        for ($k = $location_level; $k >= 1; $k--) {
            $str .= ",location_$k.name AS name$k,location_$k.id AS location_" . $k . "_id ";
        }
//
//        $q = " SELECT damage_replace.id AS did,
//            damage_replace.replaceid AS s_no,
//            damage_replace.user_id, prod_code,
//            ret_code, prod_qty, prod_value, 
//            DATE_FORMAT(date_time,'%Y-%m-%d')as date_time, 
//            task, 
//            mrp, 
//            extra_amt,
//            CONCAT_WS(' ', person.first_name,person.middle_name,person.last_name) as person_fullname,
//            dealer.name 
//            FROM person 
//            INNER JOIN damage_replace ON person.id = damage_replace.user_id 
//            INNER JOIN dealer ON damage_replace.dis_code=dealer.id 
//            INNER JOIN user_dealer_retailer udr on 
//            udr.user_id=person.id INNER JOIN dealer_location_rate_list dlrl ON dlrl.dealer_id=udr.dealer_id 
//            INNER JOIN location_view ON 
//            location_view.l5_id=dlrl.location_id 
//             $filterstr";
         $q = " SELECT damage_replace.id AS did,
            damage_replace.replaceid AS s_no,
            damage_replace.user_id, prod_code,
            replaceid, prod_qty, prod_value, 
            DATE_FORMAT(date_time,'%Y-%m-%d')as date_time, 
            task, 
            mrp, 
            extra_amt,
            CONCAT_WS(' ', person.first_name,person.middle_name,person.last_name) as person_fullname,
            dealer.name 
            FROM person 
            INNER JOIN damage_replace ON person.id = damage_replace.user_id 
            INNER JOIN dealer ON damage_replace.dis_code=dealer.id 
            INNER JOIN location_view ON location_view.state_id=person.state_id 
            
             $filterstr";

            
       //h1($q);
        list($opt, $rs) = run_query($dbc, $q, 'multi');
        if (!$opt)
            return $out;

        while ($row = mysqli_fetch_assoc($rs)) {
           
            //$id = $row['user_id'].$row['usolid'].$row['did'];
            $id = $row['did'];
            $out[$id] = $row;
            $out[$id]['date_time'] = $row['date_time'];
            $out[$id]['first_name'] = $row['first_name'];
            $out[$id]['name'] = $row['name'];
            $out[$id]['s_no'] = $row['s_no'];
            $out[$id]['ret_code'] = $row['ret_code'];
            $out[$id]['prod_qty'] = $row['prod_qty'];
            $out[$id]['prod_value'] = $row['prod_value'];
            $out[$id]['task'] = $row['task'];
            $out[$id]['mrp'] = $row['mrp'];
            $out[$id]['extra_amt'] = $row['extra_amt'];
            $out[$id]['product_name'] = $this->get_product_name($row['prod_code']);
        }
       
        //pre($out);
        return $out;
    }

################################## damage & replace ###############################################
    ////////////////////////////////?DAMAGE DETAILS//////////////////////////////////////////
    
     public function get_damage_details_report_list1($filter = '', $records = '', $orderby = '', $last_level_loc = '', $lpc_filter = '', $date_filter = '') {
        global $dbc;
        $out = array();
        $filterstr = $this->oo_filter($filter, $records, $orderby);
        // $date_filterstr = $this->oo_filter($date_filter, $records='');
//                h1($filterstr);
        $lpc_filterstr = $this->oo_filter($lpc_filter, $records = '', $orderby = '');
        $location_level = $_SESSION[SESS . 'constant']['location_level'];
        //$dynamic_loc = $location_level - 1 ;
        $str = '';
        for ($k = $location_level; $k >= 1; $k--) {
            $str .= ",location_$k.name AS name$k,location_$k.id AS location_" . $k . "_id ";
        }
//
        $q = " SELECT damage_order.id AS did,
            damage_order.replaceid AS s_no,
            damage_order.user_id, prod_code,
            ret_code, prod_qty, prod_value, 
            DATE_FORMAT(date_time,'%Y-%m-%d')as date_time, 
            task, 
            mrp, 
            extra_amt,
            CONCAT_WS(' ', person.first_name,person.middle_name,person.last_name) as person_fullname,
            dealer.name 
            FROM person 
            INNER JOIN damage_replace ON person.id = damage_replace.user_id 
            INNER JOIN dealer ON damage_order.dis_code=dealer.id 
            INNER JOIN user_dealer_retailer udr on 
            udr.user_id=person.id INNER JOIN dealer_location_rate_list dlrl ON dlrl.dealer_id=udr.dealer_id 
            INNER JOIN location_view ON 
            location_view.l5_id=dlrl.location_id 
             $filterstr";


       h1($q);
        list($opt, $rs) = run_query($dbc, $q, 'multi');
        if (!$opt)
            return $out;

        while ($row = mysqli_fetch_assoc($rs)) {
            //$id = $row['user_id'].$row['usolid'].$row['did'];
            $id = $row['did'];
            $out[$id] = $row;
            $out[$id]['date_time'] = $row['date_time'];
            $out[$id]['first_name'] = $row['first_name'];
            $out[$id]['name'] = $row['name'];
            $out[$id]['s_no'] = $row['s_no'];
            $out[$id]['ret_code'] = $row['ret_code'];
            $out[$id]['prod_qty'] = $row['prod_qty'];
            $out[$id]['prod_value'] = $row['prod_value'];
            $out[$id]['task'] = $row['task'];
            $out[$id]['mrp'] = $row['mrp'];
            $out[$id]['extra_amt'] = $row['extra_amt'];
            $out[$id]['product_name'] = $this->get_product_name($row['prod_code']);
        }
        //pre($out);
        return $out;
    }
    ///////////////////////////////////////////////////////////////////////////////////////
    
    
      public function get_damage_details_report_list($filter = '', $records = '', $orderby = '', $last_level_loc = '', $lpc_filter = '', $date_filter = '') {
        global $dbc;
        $out = array();
        $filterstr = $this->oo_filter($filter, $records, $orderby);
        // $date_filterstr = $this->oo_filter($date_filter, $records='');
//                h1($filterstr);
        $lpc_filterstr = $this->oo_filter($lpc_filter, $records = '', $orderby = '');
        $location_level = $_SESSION[SESS . 'constant']['location_level'];
        //$dynamic_loc = $location_level - 1 ;
        $str = '';
        for ($k = $location_level; $k >= 1; $k--) {
            $str .= ",location_$k.name AS name$k,location_$k.id AS location_" . $k . "_id ";
        }
//
        $q = "SELECT do.id AS id,
            do.complaint_id AS cid,
            DATE_FORMAT(do.ch_date,'%Y-%m-%d') AS date,
            do.ch_dealer_id AS did,
            dod.product_id AS pid,
            dod.product_rate AS rate,
            dod.mrp AS mrp,
            dod.qty AS qty
            FROM damage_order AS do
            INNER JOIN damage_order_details AS dod ON do.id=dod.ch_id
            $filterstr";

     // h1($q);
      
        list($opt, $rs) = run_query($dbc, $q, 'multi');
        if (!$opt)
            return $out;
          while ($row = mysqli_fetch_assoc($rs)) {
            //$id = $row['user_id'].$row['usolid'].$row['did'];
            $id = $row['id'];
            $out[$id] = $row;
            $out[$id]['date'] = $row['date'];
            $out[$id]['rate'] = $row['rate'];
         //   $out[$id]['did'] = $row['did'];
           // $out[$id]['pid'] = $row['pid'];
           // $out[$id]['cid'] = $row['cid'];
            $out[$id]['qty'] = $row['qty'];
            //$out[$id]['qty'] = $row['qty'];
            $out[$id]['pid'] = myrowval('catalog_product', 'name',"id=".$row['pid']);
            $out[$id]['did'] = myrowval('dealer', 'name', "id=".$row['did']);
            $out[$id]['cid'] =  myrowval('complaint_type','name',"id=".$row['cid']);
        }
       // pre($out);
        return $out;
      }
    
    
    
    ///////////////////////////////END OF DAMAGE DETAILS///////////////////////////////////
      /////////////////////////////Start of Damage SEY/////////////////////////////////////////////////
    
    
      public function damage_update($did) {
        global $dbc;
         $q = "UPDATE `damage_order` SET `damage_set` = '1' WHERE `damage_order`.`id` = $did";
            $rs = mysqli_query($dbc,$q); 
        return $rs;
    }
    
    
    
    ///////////////////////////////END OF Damage SET DETAILS///////////////////////////////////
  
    
     //////////////////////////////START OF CHALAN DETAILS//////////////////////////////////////////////////
    
    
      public function get_chalan_details_report_list($filter = '', $records = '', $orderby = '') {
        global $dbc;
        $out = array();
      // echo"hgfgf";
        $filterstr = $this->oo_filter($filter, $records, $orderby);
        // $date_filterstr = $this->oo_filter($date_filter, $records='');
//                h1($filterstr);
        $lpc_filterstr = $this->oo_filter($lpc_filter, $records = '', $orderby = '');
        $location_level = $_SESSION[SESS . 'constant']['location_level'];
        //$dynamic_loc = $location_level - 1 ;
        $str = '';
        for ($k = $location_level; $k >= 1; $k--) {
            $str .= ",location_$k.name AS name$k,location_$k.id AS location_" . $k . "_id ";
        }
//
        $q = "SELECT cod.id AS id,
                uso.order_id AS orderid, 
                uso.total_sale_value AS salevalue,
                SUM(cod.taxable_amt) AS tax,
                co.ch_dealer_id AS did 
                FROM user_sales_order AS uso
                INNER JOIN challan_order_details AS cod ON uso.order_id=cod.order_id
                INNER JOIN challan_order AS co ON cod.ch_id=co.id
            $filterstr GROUP BY uso.order_id";

    // h1($q);
      
        list($opt, $rs) = run_query($dbc, $q, 'multi');
        if (!$opt)
            return $out;
          while ($row = mysqli_fetch_assoc($rs)) {
            //$id = $row['user_id'].$row['usolid'].$row['did'];
            $id = $row['id'];
            $out[$id] = $row;
            $out[$id]['orderid'] = $row['orderid'];
            $out[$id]['salevalue'] = $row['salevalue'];
            $out[$id]['tax'] = $row['tax'];
            $out[$id]['pid']= myrowval('catalog_product', 'name',$row['pid']);
            $out[$id]['did'] = myrowval('dealer', 'name', $row['did']);
            $out[$id]['cid']=  myrowval('complaint','complaint',$row['cid']);
        }
       // pre($out);
        return $out;
    }
    
    
    
    ///////////////////////////////END OF CHALAN DETAILS///////////////////////////////////
     public function get_no_attendance_list($filter = '', $records = '', $orderby = '', $date = '') {
        global $dbc;
        $out = array();
        $date_filter = join(' AND ', $date);
        // if user has send some filter use them.
        $filterstr = $this->oo_filter($filter, $records, $orderby);
        $q = "SELECT distinct p.id,p.*,l3_name as state,r.rolename,l4_id,l4_name             
            FROM person p 
            INNER JOIN person_login pl ON p.id=pl.person_id 
            INNER JOIN _role r ON p.role_id=r.role_id 
            INNER JOIN dealer_location_rate_list dlrl ON dlrl.dealer_id=p.id
            INNER JOIN dealer ON dealer.id=dlrl.dealer_id
            INNER JOIN location_view lv ON lv.l5_id=dlrl.location_id
            $filterstr AND p.id not in 
            (select distinct user_id from user_daily_attendance where $date_filter ) 
            AND role_group_id='11' AND r.role_id<>'1' AND pl.person_status='1' ORDER BY l4_id ASC ";
      //  h1($q);
        $rs = mysqli_query($dbc, $q);
        //if(!$opt) return $out; // if no order placed send blank array
        while ($row = mysqli_fetch_assoc($rs)) {
            $id = $row['id'];
            $out[$id] = $row;
            $out[$id]['person_id_senior']=$row['person_id_senior'];
            $out[$id]['senior'] = $this->get_username($row[person_id_senior]);
        }
       //  pre($out);
        return $out;
    }
    
   
    public function get_no_sale_list($filter = '', $records = '', $orderby = '', $date = '') {
        global $dbc;
        $out = array();
        //$date_filter = join(' AND ', $date);
        $uso_date = "DATE_FORMAT(`date`,'" . MYSQL_DATE_SEARCH . "') = '$date'";
        $uda_date = "DATE_FORMAT(`work_date`,'" . MYSQL_DATE_SEARCH . "') = '$date'";
        // if user has send some filter use them.
        $filterstr = $this->oo_filter($filter, $records, $orderby);
        $q = "SELECT distinct p.id,p.*,l3_name as state,r.rolename,l4_id as zone_id,ws.name as wstatus              
            , DATE_FORMAT(`work_date`,'%H:%i:%s') as wtime FROM user_daily_attendance uda 
            INNER JOIN person p ON p.id=uda.user_id
            INNER JOIN person_login pl ON p.id=pl.person_id 
            INNER JOIN _role r ON p.role_id=r.role_id 
            INNER JOIN _working_status ws ON ws.id=uda.work_status
            INNER JOIN dealer_location_rate_list dlrl ON dlrl.user_id=p.id
            INNER JOIN dealer ON dealer.id=dlrl.dealer_id
            INNER JOIN location_view lv ON lv.l5_id=dlrl.location_id
            $filterstr AND uda.user_id NOT IN 
            (select distinct user_id from user_sales_order where $uso_date ) 
            AND role_group_id='11' AND r.role_id<>'1' AND pl.person_status='1' AND $uda_date AND uda.work_status NOT IN(5,6) ORDER BY zone_id ASC ";
        // h1($q);
        $rs = mysqli_query($dbc, $q);
        //if(!$opt) return $out; // if no order placed send blank array
        while ($row = mysqli_fetch_assoc($rs)) {
            $id = $row['id'];
            $out[$id] = $row;
            $out[$id]['senior'] = myrowval('person', 'person_fullname', 'id=' . $row[person_id_senior]);
        }
        // pre($out);
        return $out;
    }
    
      
///////////////////////////////////////RETAILER CLAIM/////////////////////////////////////////////////////
    
      public function get_claim_retailer_list($filter='', $records='', $orderby='') {
        global $dbc;
       //echo "ANKUSH";exit;
        $out = array();
        $a = array();
        $ch = array();
      //  $dealer_id = $_SESSION[SESS.'data']['dealer_id'];
      // pre($filter);
        $filterstr = $this->oo_filter($filter, $records, $orderby);
       // print_r($filterstr);
       // $mtype = $_SESSION[SESS . 'constant']['retailer_level'];
      //  $q = "SELECT ch_retailer_id,challan_order.id as cid,istarget_claim,`ch_dealer_id`,DATE_FORMAT(`ch_date`,'%M-%Y') as myear,DATE_FORMAT(`ch_date`,'%m') as month,DATE_FORMAT(`ch_date`,'%Y-%m') as my,SUM(taxable_amt) as sale FROM `challan_order` INNER JOIN `challan_order_details` cod ON cod.ch_id=challan_order.id WHERE ch_dealer_id='$dealer_id' GROUP BY `ch_retailer_id` ASC";
      //  h1($q);
       $q ="SELECT name,id FROM `retailer` $filterstr AND retailer_status='1'";
       //h1($q);
       $rs = mysqli_query($dbc,$q);
       //$rs = mysqli_query($dbc,$qq);
        
        while ($row = mysqli_fetch_assoc($rs)) {
            $id = $row['id'];
           // $out[$id]['rid'] = $row['name'];
            $out[$id]['retailer_name'] = $row['name'];
            $ch[$id]['challan'] = $this->get_challan($id);
            $out[$id]['sale'] =  $ch[$id]['challan']['sale'];
            $out[$id]['ch_dealer_id'] =  $ch[$id]['challan']['ch_dealer_id'];
         
            $a[$id]['scheme'] = $this->current_scheme($out[$id]['sale']);
            $out[$id]['value'] =  $a[$id]['scheme']['value'];
            $out[$id]['value_to'] =  $a[$id]['scheme']['value_to'];
            $out[$id]['scheme_gift'] =  $a[$id]['scheme']['scheme_gift'];
            $out[$id]['start_date'] =  $a[$id]['scheme']['start_date'];
            $out[$id]['end_date'] =  $a[$id]['scheme']['end_date'];
            }// while($row = mysqli_fetch_assoc($rs)){ ends
     // pre($out);
        return $out;
    }
    
    public function get_challan($rid)
    {
        global $dbc;
        $q ="SELECT ch_retailer_id,challan_order.id as cid,istarget_claim,`ch_dealer_id`,DATE_FORMAT(`ch_date`,'%M-%Y') as myear,DATE_FORMAT(`ch_date`,'%m') as month,DATE_FORMAT(`ch_date`,'%Y-%m') as my,SUM(taxable_amt) as sale FROM `challan_order` INNER JOIN `challan_order_details` cod ON cod.ch_id=challan_order.id WHERE ch_retailer_id='$rid' GROUP BY `ch_retailer_id` ASC";
        //h1($q);
        $r=  mysqli_query($dbc, $q);
        
        $row = mysqli_fetch_assoc($r);
        
        return $row;
    }
    
     public function current_scheme($sale) {
        //pre($_SESSION);
        $state = $_SESSION[SESS.'data']['state_id'];
        global $dbc;   
        $date = date('Y-m-d');
        $q = "SELECT svp.id,svp.value,svp.value_to,svp.scheme_gift,svpd.start_date,svpd.end_date from scheme_value_product_details svp INNER JOIN scheme_value svpd ON svpd.scheme_id = svp.scheme_id where '$date' BETWEEN svpd.start_date AND svpd.end_date AND user = 3 AND state_id = $state AND '$sale' BETWEEN svp.value AND svp.value_to";
      // h1($q);
        $r = mysqli_query($dbc, $q);
      $row = mysqli_fetch_assoc($r);
        //pre($row);
        return $row;
    }
    
    

public function get_user_detailed_report_list($filter = '', $records = '', $orderby = '', $date_filter = '') {
$holder = array();
global $dbc;
$out = array();
$filterstr = $this->oo_filter($filter, $records, $orderby);
$location_level = $_SESSION[SESS . 'constant']['location_level'];
$q="SELECT user_sales_order.user_id, _role.rolename,
l5_name AS name5,
l5_id AS location_5_id,
SUM(rate * quantity)as total_sale,
l4_name as name4,
l4_id as zoneid,
l2_name as name2,
DATE_FORMAT(date,'%d-%M-%Y') as dates,
DATE_FORMAT(date,'%Y-%m-%d') as `date`,
user_sales_order.id AS uniq, 
user_sales_order.dealer_id AS dealer_id,person_id_senior,
CONCAT_WS(' ',first_name, middle_name, last_name) AS person_name,
dealer.name AS dealer_name,user_sales_order.location_id AS usolid,
user_sales_order.dealer_id AS did,count(user_sales_order.retailer_id) AS retailerc
FROM user_sales_order INNER JOIN user_sales_order_details USING(order_id)
INNER JOIN person ON person.id = user_sales_order.user_id 
INNER JOIN _role USING(role_id) 
INNER JOIN dealer ON dealer.id = user_sales_order.dealer_id
INNER JOIN retailer ON user_sales_order.retailer_id = retailer.id 
INNER JOIN location_view lv ON lv.l5_id=user_sales_order.location_id $filterstr GROUP BY user_sales_order.`date`,user_sales_order.user_id 
ORDER BY user_sales_order.`date` ASC,zoneid ASC,l5_name ASC,person_name ASC" ;
// h1($q);
list($opt, $rs) = run_query($dbc, $q, 'multi');
if (!$opt)
return $out;
$ankush = 0;
while ($row = mysqli_fetch_assoc($rs)) {
$id = $row['uniq'];
$ankush++;
$out[$id] = $row;

$out[$id]['senior_person_name'] = myrowval($table = "person", $col = "CONCAT_WS(' ', first_name,middle_name,last_name)", $where = "id = '$row[person_id_senior]'");
// echo "aaaaaaaaaaa";
$out[$id]['effetive_coverage_area'] = $this->get_effective_coverage_area($row['date'], $row['user_id'], $row['usolid'], $row['did']);
// echo "bbbbbbbbbbb";
$out[$id]['total_coverage_area'] = $this->get_total_coverage_area($row['usolid']);
// echo "ccccccccccccc";
$out[$id]['lpc'] = $this->get_total_lpc($row['user_id'], $row['usolid'], $row['date'], $row['did']);
// echo "ddddddddddd";
$out[$id]['total_sale'] = $this->get_total_sale($row['user_id'],$row['date'], $row['did']);
// echo "eeeeeeeeeeeeee";
$holder = $this->get_call_details($row['user_id'], $row['usolid'], $row['date'], $row['did']);
$holder1 = $this->get_attendence_details($row['user_id'],$row['date']);
$holder2 = $this->get_retailer_count($row['user_id'],$row['location_5_id']);
$holder3 = $this->get_dealer_count($row['user_id']);
$holder4 = $this->get_user_expense_total($row['user_id']);
$holder5 = $this->get_checkout_details($row['user_id'],$row['date']);
$holder6 = $this->get_not_contacted_details($row['user_id'], $row['usolid'], $row['date'], $row['did']);
// echo "ffffffffffff";
$out[$id]['total_call'] = $holder[0];
$out[$id]['productive'] = $holder[1];
$out[$id]['non_productive'] = $holder[2];
$out[$id]['time'] = $holder1[0];
$out[$id]['ret'] = $holder2[0];
$out[$id]['del'] = $holder3[0];
$out[$id]['total_expense'] = $holder4[0];
$out[$id]['total_call1']=$holder6[0];
$nc= $out[$id]['ret']-$out[$id]['total_call1'];
if($nc<=0)
$nc=0;
$out[$id]['non_contacted'] = $nc;
$out[$id]['checkout_time'] = $holder5[0];
}
// echo $ankush;
// pre($out);
return $out;
}
public function get_not_contacted_details($user, $location, $date, $dealer) {
global $dbc;
$out = NULL;

$q = "SELECT retailer_id,call_status FROM user_sales_order WHERE user_id = '$user' AND dealer_id = '$dealer' AND date='$date' AND location_id = '$location' group by retailer_id";
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
// pre($temp);
return $temp;
}
public function get_attendence_details($user,$date) {
global $dbc;
$out = NULL;

$q = "SELECT user_id,DATE_FORMAT(work_date,'%d-%m-%Y') AS date,cast(`work_date` as time) AS time FROM `user_daily_attendance` WHERE user_id = '$user' AND DATE_FORMAT(work_date,'%Y-%m-%d')='$date'";
// h1($q);
list($opt, $rs) = run_query($dbc, $q, 'multi');
if (!$opt)
return $out;
while ($rows = mysqli_fetch_array($rs)) {
$time=$rows[time];
}
$temp = array();
$temp[0] = $time;
// pre($temp);
return $temp;
}


public function get_checkout_details($user,$date) {
global $dbc;
$out = NULL;

$q = "SELECT user_id,DATE_FORMAT(work_date,'%d-%m-%Y') AS date,cast(`work_date` as time) AS ctime FROM `check_out` WHERE user_id = '$user' AND DATE_FORMAT(work_date,'%Y-%m-%d')='$date'";
// h1($q);
list($opt, $rs) = run_query($dbc, $q, 'multi');
if (!$opt)
return $out;
while ($rows = mysqli_fetch_array($rs)) {
$time=$rows[ctime];
}
$temp = array();
$temp[0] = $time;
// pre($temp);
return $temp;
}

public function get_user_expense_total($user) {
global $dbc;
$out = NULL;

$q = "SELECT (`travelling_allowance`+`drawing_allowance`+`other_expense`+`rent`) AS total FROM `user_expense_report` WHERE `person_id` = '$user'";
//h1($q);
list($opt, $rs) = run_query($dbc, $q, 'multi');
if (!$opt)
return $out;
while ($rows = mysqli_fetch_array($rs)) {
$ret=$rows[total];
}
$temp = array();
$temp[0] = $ret;
// pre($temp);
return $temp;
}

public function get_dealer_count($user) {
global $dbc;
$out = NULL;

$q = "SELECT count(DISTINCT dealer.id) AS del FROM `dealer` INNER JOIN user_dealer_retailer ON dealer.id=user_dealer_retailer.dealer_id WHERE user_id = '$user'";
// h1($q);
list($opt, $rs) = run_query($dbc, $q, 'multi');
if (!$opt)
return $out;
while ($rows = mysqli_fetch_array($rs)) {
$ret=$rows[del];
}
$temp = array();
$temp[0] = $ret;
// pre($temp);
return $temp;
}


public function get_category_performance_list($filter = '', $records = '', $orderby = '', $date_filter = '') {
        $holder = array();
        global $dbc;
        $out = array();
        $filterstr = $this->oo_filter($filter, $records, $orderby);
        $location_level = $_SESSION[SESS . 'constant']['location_level'];
//        $q="SELECT usod.rate,usod.quantity,usod.product_id,usod.order_id,uso.order_id,uso.date,uso.location_id,cv.product_name AS sku,"
//                . " cv.c1_name AS category,lv.l4_name AS region"
//                . " FROM user_sales_order AS uso INNER JOIN user_sales_order_details AS usod ON uso.order_id = usod.order_id"
//                . " INNER JOIN catalog_view AS cv ON usod.product_id = cv.product_id"
//                . " INNER JOIN dealer_location_rate_list AS dlrl ON dlrl.location_id=uso.location_id"
//                . " INNER JOIN location_view AS lv ON lv.l5_id $filterstr GROUP BY date,product_id" ;
        $q="SELECT usod.id as id,usod.rate as rate,SUM(usod.quantity) as quantity,usod.product_id as product_id,uso.location_id as location_id,cv.product_name AS sku,"
                . " cv.c1_name AS category,lv.l4_name AS region,lv.l2_name AS state"
                . " FROM user_sales_order_details  AS usod  INNER JOIN user_sales_order AS uso ON uso.order_id = usod.order_id"
                . " INNER JOIN catalog_view AS cv ON usod.product_id = cv.product_id" 
                . " INNER JOIN catalog_product ON catalog_product.id=cv.product_id"               
                . " INNER JOIN location_view AS lv ON lv.l5_id = uso.location_id  "
                . " INNER JOIN state ON state.stateid = lv.state_id "
                . "$filterstr GROUP BY stateid,product_id" ;
 //h1($q);
        list($opt, $rs) = run_query($dbc, $q, 'multi');
        if (!$opt)
            return $out;

        while ($row = mysqli_fetch_assoc($rs)) {
            $id = $row['id'];
           
            $out[$id] = $row;         
        }
        //  pre($out);exit;
        return $out;
    }
    
     
    
    public function get_statelevel_performance_list($start,$end, $records = '', $orderby = '') {
        global $dbc;
        $out = array();
        
       // print_r($filter);
      //  $state=$filter[1];
           
      //   $filterstr = $this->oo_filter($filter, $records, $orderby);  
         // $filter_isr = $this->oo_filter($filterisr, $records, $orderby); 
         // $filter_out = $this->oo_filter($filterout, $records, $orderby); 
         // $filter_mtp = $this->oo_filter($filtermtp, $records, $orderby); 
         // $filter_uda  = $this->oo_filter($filteruda, $records, $orderby);
        $q="select stateid,statename AS state FROM state";
      //h1($q);
        $rs = mysqli_query($dbc, $q);
       // $abc = 0;
        //if(!$opt) return $out; // if no order placed send blank array
        while ($row = mysqli_fetch_assoc($rs)) {
           // $abc++;
            //pre($row);
            $id = $row['stateid'];
            $out[$id] = $row;
            
             $out[$id]['so_data']= $this->get_so_details($id,$start,$end);
             $out[$id]['outlet_value']= $this->get_newoutlet_value($id,$start,$end);
           //  $out[$id]['pro_outlet_value']= $this->get_pro_outlet_value($id,$filterstr);
             $out[$id]['so_sale_data']= $this->get_pro_order_cov($id,$start,$end);


        }   
        
        //echo $abc;
        // pre($out);
        return $out;
    } 
    public function get_so_details($id,$start,$end) {
        global $dbc;
        $out = NULL;
        
        $q="SELECT count(DISTINCT(person.id)) AS so,(SELECT count(DISTINCT(uda.id)) AS so_at FROM user_daily_attendance AS uda INNER JOIN person ON person.id=uda.user_id WHERE DATE_FORMAT(`work_date`,'%Y%m%d')>='$start' AND DATE_FORMAT(`work_date`,'%Y%m%d')<='$end' AND state_id='$id') as tot_att,(SELECT count(DISTINCT(uda1.id)) AS w_at FROM user_daily_attendance AS uda1 INNER JOIN person ON person.id=uda1.user_id WHERE DATE_FORMAT(`work_date`,'%Y%m%d')>='$start' AND DATE_FORMAT(`work_date`,'%Y%m%d')<='$end' AND state_id='$id' AND uda1.work_status IN(1,9,13,14)) as work_att ,
(SELECT count(uso.retailer_id) AS so_out FROM user_sales_order AS uso INNER JOIN person ON person.id=uso.user_id WHERE state_id='$id' AND person.role_id in (3,14,15,21) AND DATE_FORMAT(`date`,'%Y%m%d')>='$start' AND DATE_FORMAT(`date`,'%Y%m%d')<='$end') AS outlet_visited
        FROM person INNER JOIN person_login ON person.id=person_login.person_id WHERE state_id='$id' AND person_status='1' AND role_id in (2,3,4,10,11,14,15,21)";
       
     //  h1($q);
        list($opt, $rs) = run_query($dbc, $q, $mode = 'single', $msg = '');
        if (!$opt)
            return $out;
        return $rs;
    }
//     public function get_isr($id) {
//        global $dbc;
//        $out = NULL;
//        
//        $q="SELECT count(DISTINCT(person.id)) AS isr FROM person WHERE state_id='$id' AND role_id='14'";
//       
//       // h1($q);
//        list($opt, $rs) = run_query($dbc, $q, $mode = 'single', $msg = '');
//        if (!$opt)
//            return $out;
//        return $rs['isr'];
//    }   
      public function get_pro_order_cov($id,$start,$end) {
        global $dbc;
        $out = NULL;
        
        $q="SELECT count(DISTINCT uso.order_id) AS so_pro_order,SUM(rate*quantity) as ov,
        (SELECT sum(mtp.total_sales) AS mtarget FROM monthly_tour_program AS mtp INNER JOIN person ON person.id=mtp.person_id  where person.state_id='$id' AND DATE_FORMAT(`working_date`,'%Y%m%d')>='$start' AND DATE_FORMAT(`working_date`,'%Y%m%d')<='$end' AND person.role_id in (3,14,15,21)) AS mtarget
         FROM user_sales_order AS uso INNER JOIN user_sales_order_details USING(order_id) INNER JOIN person ON person.id=uso.user_id WHERE person.state_id='$id' AND person.role_id in (3,14,15,21) AND uso.call_status='1' AND DATE_FORMAT(`date`,'%Y%m%d')>='$start' AND DATE_FORMAT(`date`,'%Y%m%d')<='$end'";
      
        //h1($q);
        list($opt, $rs) = run_query($dbc, $q, $mode = 'single', $msg = '');
        if (!$opt)
            return $out;
        return $rs;
    }  
    
    
    // public function get_pro_outlet_value($id,$filterstr) {
    //     global $dbc;
    //     $out = NULL;
        
    //     $q="SELECT sum(usod.rate*usod.quantity) AS so_pro_value FROM user_sales_order AS uso INNER JOIN user_sales_order_details usod using (order_id) INNER JOIN location_view AS lv ON lv.l5_id=uso.location_id INNER JOIN person ON person.id=uso.user_id  $filterstr AND lv.state_id='$id' AND person.role_id in (3,14) AND uso.call_status='1'";
       
    //     //h1($q);
    //     list($opt, $rs) = run_query($dbc, $q, $mode = 'single', $msg = '');
    //     if (!$opt)
    //         return $out;
    //     return $rs['so_pro_value'];
    // }
    
    public function get_newoutlet_value($id,$start,$end) {
        global $dbc;
        $out = NULL;
        
        $q="SELECT sum(usod.rate*usod.quantity) AS outlet_value,count(DISTINCT retailer.id) AS no FROM retailer "
                . "INNER JOIN user_sales_order AS uso ON uso.retailer_id=retailer.id "
                . "INNER JOIN user_sales_order_details usod using (order_id) "
                
                . "INNER JOIN person ON person.id=uso.user_id where DATE_FORMAT(`date`,'%Y%m%d')>='$start' AND DATE_FORMAT(`date`,'%Y%m%d')<='$end' AND person.state_id='$id' AND DATE_FORMAT(`created_on`,'%Y%m%d')>='$start' AND DATE_FORMAT(`created_on`,'%Y%m%d')<='$end' AND person.role_id in (3,14,15,21)";
       
        //h1($q);
        list($opt, $rs) = run_query($dbc, $q, $mode = 'single', $msg = '');
        if (!$opt)
            return $out;
        return $rs;
    }
    
    
    
///////////////////////////////////////CLOSE RETAILER CAIM////////////////////////////////////////////////
  public function get_dropped_outlet_list($filter = '', $records = '', $orderby = '', $date_filter = '',$lastedate_filter ='',$rdate_filter='',$usodate='',$ldate = '',$smonth='') {
        global $dbc;
        $out = array();
        $holder = array();
      
         $uid = $_SESSION[SESS . 'data']['id'];
        
          $filternew = $this->oo_filter($filter, $records, $orderby); 
           $filterstr = $this->oo_filter($date_filter, $records, $orderby);  
           $lastedatefilter = $this->oo_filter($lastedate_filter, $records, $orderby); 
           $rdatefilter = $this->oo_filter($rdate_filter, $records, $orderby); 
           
           $del="DELETE FROM `_temp_dropped_outlet` WHERE user_id='$uid'";
         $rdel = mysqli_query($dbc, $del);
           
           $insertc = "insert into _temp_dropped_outlet(user_id,pid,retailer_id,month)(select $uid,uso.user_id,uso.retailer_id,date_format(uso.date,'%m') as month FROM person "
                   . "INNER JOIN person_login ON person_login.person_id = person.id AND person_status ='1' "
                   . "INNER JOIN user_sales_order AS uso ON uso.user_id=person.id "
                   . "$filternew AND $usodate and uso.retailer_id not in (select id from retailer $rdatefilter) group by uso.retailer_id)";
           // h1($insertc); 
           $insertcrun = mysqli_query($dbc, $insertc);
           
            $insertl = "insert into _temp_dropped_outlet(user_id,pid,retailer_id,month)(select $uid,uso.user_id,uso.retailer_id,date_format(uso.date,'%m') as month FROM person "
                   . "INNER JOIN person_login ON person_login.person_id = person.id AND person_status ='1' "
                   . "INNER JOIN user_sales_order AS uso ON uso.user_id=person.id "
                   . "$filternew AND $ldate group by uso.retailer_id)";
           // h1($insertl); //exit;   
            $insertlrun = mysqli_query($dbc, $insertl);
            
            $lsmonth = $smonth -1;
            
            $up = "update _temp_dropped_outlet temp set temp.dropped= 1 "
                    . "where temp.user_id='$uid' and temp.month='$smonth' and temp.retailer_id in "
                    . "(SELECT * FROM(SELECT ntmp.retailer_id FROM _temp_dropped_outlet ntmp                    
                    WHERE ntmp.month='$lsmonth' and ntmp.user_id='$uid')tblTmp)  ";
            //  h1($up);
             $r11 = mysqli_query($dbc, $up);
            
            
                $q="SELECT person.id AS pid,CONCAT_WS(' ',first_name, middle_name, last_name) AS person_fullname, 
                    person.emp_code,person.person_id_senior,person.state_id,state.statename AS state FROM person  
                     INNER JOIN person_login ON person_login.person_id = person.id AND person_status ='1'   
                     INNER JOIN state ON state.stateid=person.state_id 
                     $filternew";
                
             //   h1($q);
        $rs = mysqli_query($dbc, $q);
       // $abc = 0;
        //if(!$opt) return $out; // if no order placed send blank array
        while ($row = mysqli_fetch_assoc($rs)) {
           // $abc++;
            //pre($row);
            $id = $row['pid'];
            $out[$id] = $row;
            $out[$id]['senior_person_name'] = myrowval($table = "person", $col = "CONCAT_WS(' ', first_name,middle_name,last_name)", $where = "id = '$row[person_id_senior]'");
            $location = $row['beat_id'];
            $out[$id]['new_outlet_billed']= $this->get_new_outlet_billed($row[pid],$rdatefilter,$usodate);
            $out[$id]['total_outlet_billed']= $this->get_total_outlet_billed($row[pid],$filterstr,$rdatefilter);
            $out[$id]['pre_outlet_billed']= $this->get_pre_outlet_billed($row[pid],$lastedatefilter);
            $out[$id]['dropped_outlets']= $this->get_dropped_outlets($row[pid],$uid);

        } 
        // pre($out);
        return $out;
    }
    
     public function get_dropped_outlets($pid,$uid) {
        global $dbc;
        $out = NULL;
                
      $q="SELECT count(id) AS dropped_outlets FROM _temp_dropped_outlet where user_id=$uid AND pid=$pid and dropped='1'";         
      // h1($q);
        list($opt, $rs) = run_query($dbc, $q, $mode = 'single', $msg = '');
        if (!$opt)
            return $out;
        return $rs['dropped_outlets'];
    } 
    
    
     public function get_new_outlet_billed($id,$filterstr,$usodate) {
        global $dbc;
        $out = NULL;
                
      $q="SELECT count(distinct uso.retailer_id) AS new_outlet_billed FROM retailer "
              . "INNER JOIN user_sales_order AS uso ON uso.retailer_id=retailer.id $filterstr "
              . "AND uso.user_id=$id AND $usodate";
         
      // h1($q);
        list($opt, $rs) = run_query($dbc, $q, $mode = 'single', $msg = '');
        if (!$opt)
            return $out;
        return $rs['new_outlet_billed'];
    }  
    
    
    public function get_total_outlet_billed($id,$filterstr,$rdatefilter) {
        global $dbc;
        $out = NULL;
        
   
        $q="SELECT count(distinct uso.retailer_id) AS total_outlet_billed FROM user_sales_order AS uso $filterstr "
                . "AND uso.user_id=$id AND uso.call_status='1' and uso.retailer_id not in (select id from retailer $rdatefilter)";
       //h1($q);
        list($opt, $rs) = run_query($dbc, $q, $mode = 'single', $msg = '');
        if (!$opt)
            return $out;
        return $rs['total_outlet_billed'];
    }
   
    public function get_pre_outlet_billed($id,$filterstr) {
        global $dbc;
        $out = NULL;
      
        $q="SELECT count(distinct uso.retailer_id) AS pre_outlet_billed FROM user_sales_order AS uso $filterstr "
                . "AND uso.user_id=$id AND uso.call_status='1'";
      // h1($q);
        list($opt, $rs) = run_query($dbc, $q, $mode = 'single', $msg = '');
        if (!$opt)
            return $out;
        return $rs['pre_outlet_billed'];
    }
    
    public function get_dropped_sku_list($filter = '', $records = '', $orderby = '',$lastedate_filter = '',$rdate_filter = '',$usodate ='',$filterlast='',$smonth) {
        global $dbc;
        $out = array();
         $uid = $_SESSION[SESS . 'data']['id'];
           $filterstr = $this->oo_filter($filter, $records, $orderby);       
           $lastedatefilter = $this->oo_filter($lastedate_filter, $records, $orderby);        
           $uso_date = $this->oo_filter($usodate, $records, $orderby); 
           $filter_last = $this->oo_filter($filterlast, $records, $orderby);
           
             $del="DELETE FROM `_temp_dropped_sku_outlet` WHERE user_id='$uid'";
            $rdel = mysqli_query($dbc, $del);
         
            $insertc = "insert into _temp_dropped_sku_outlet(user_id,pid,retailer_id,product_id,month)(select $uid,uso.user_id,uso.retailer_id,usod.product_id,date_format(uso.date,'%m') as month FROM user_sales_order AS uso "
                    . "INNER JOIN user_sales_order_details AS usod ON usod.order_id=uso.order_id "
                    . "INNER JOIN  person ON person.id = uso.user_id "
                    . "INNER JOIN person_login ON person_login.person_id = person.id AND person_status ='1' "
                   . "$filterstr AND uso.call_status='1' AND uso.retailer_id not in (select id from retailer where $rdate_filter))";
          // h1($insertc);
            $insertcrun = mysqli_query($dbc, $insertc);
           
            $insertl = "insert into _temp_dropped_sku_outlet(user_id,pid,retailer_id,product_id,month)(select $uid,uso.user_id,uso.retailer_id,usod.product_id,date_format(uso.date,'%m') as month FROM user_sales_order AS uso "
                   . "INNER JOIN user_sales_order_details AS usod ON usod.order_id=uso.order_id "
                    . "INNER JOIN person ON person.id=uso.user_id "
                    . "INNER JOIN person_login ON person_login.person_id = person.id AND person_status ='1' "
                   . "$filter_last AND uso.call_status='1')";
           // h1($insertl);   
            $insertlrun = mysqli_query($dbc, $insertl);
            
             $lsmonth = $smonth -1;
            
            
           $q="SELECT id AS pid,name AS product_name FROM catalog_product";
          // h1($q);
        $rs = mysqli_query($dbc, $q);
        $abc = 0;
        //if(!$opt) return $out; // if no order placed send blank array
        while ($row = mysqli_fetch_assoc($rs)) {          
           
            $id = $row['pid'];
             $up = "update _temp_dropped_sku_outlet temp set temp.dropped= 1 "
                    . "where temp.user_id='$uid' and temp.month='$smonth' and temp.product_id='$id' and temp.retailer_id in "
                    . "(SELECT * FROM(SELECT ntmp.retailer_id FROM _temp_dropped_sku_outlet ntmp                    
                    WHERE ntmp.month='$lsmonth' and ntmp.user_id='$uid' and ntmp.product_id='$id')tblTmp)  ";
            //  h1($up);
             $r11 = mysqli_query($dbc, $up);
            $out[$id] = $row;           
            $out[$id]['person']= $this->get_person_name($row[pid],$filterstr,$lastedatefilter,$rdate_filter,$uso_date,$filter_last);
             $abc++;
        }   
        
        return $out;
    }
    
    
    
    
    /**************************New report 26th Aug 2017***************************************/
    
     public function get_dropped_product_list($filter = '', $records = '', $orderby = '',$filterdate = '',$lastedate_filter = '',$rdate_filter = '',$usodate ='') {
        global $dbc;
        $out = array();
        
         $filterstr = $this->oo_filter($filter, $records, $orderby); 
           $filter_date = $this->oo_filter($filterdate, $records, $orderby);  
           $lastedatefilter = $this->oo_filter($lastedate_filter, $records, $orderby); 
           $rdatefilter = $this->oo_filter($rdate_filter, $records, $orderby); 
           
                
           $q="SELECT id AS pid,name AS product_name FROM catalog_product";
          // h1($q);
        $rs = mysqli_query($dbc, $q);
        $abc = 0;
        //if(!$opt) return $out; // if no order placed send blank array
        while ($row = mysqli_fetch_assoc($rs)) {          
           
            $id = $row['pid'];
            $out[$id] = $row;           
            $out[$id]['person']= $this->get_uso_name($row[pid],$filterstr,$filter_date,$lastedatefilter,$rdatefilter,$usodate);
             $abc++;
        }   
        pre($out); exit;
        return $out;
    }
    
      public function get_uso_name($id,$filterstr,$filter_date,$lastedate_filter,$rdatefilter,$usodate) {
        global $dbc; 
          $out =array();
        //$pre_mth = Date("Y-m", strtotime($date . " last month"));
        $pid=$id;
        /*$q="SELECT uso.id as oid,usod.product_id AS id
            ,state.statename AS state,uso.date as ch_date,uso.location_id FROM user_sales_order uso
            LEFT JOIN user_sales_order_details AS usod ON usod.order_id=uso.order_id 
            LEFT JOIN location_view AS lv ON lv.l5_id=uso.location_id 
            INNER JOIN state ON state.stateid=lv.state_id
            $filterstr AND usod.product_id='$pid'";*/
        
         $q="SELECT state.statename AS state,state.stateid as state_id FROM state $filterstr";
       
       //   h1($q); exit;
       $rs = mysqli_query($dbc,$q);
      
        while($row=  mysqli_fetch_assoc($rs)){
            $id=$row['state_id'];
            $out[$id]['state']=$row['state'];            
            $location=$row['state_id'];
            $out[$id]['pre_outlet_billed']= $this->get_product_pre_outlet_billed($lastedate_filter,$location,$pid);
            $out[$id]['new_outlet_billed']= $this->get_product_new_outlet_billed($rdatefilter,$location,$pid,$usodate);
            $out[$id]['total_outlet_billed']= $this->get_product_total_outlet_billed($filter_date,$location,$pid);
        }
         
   //     pre($out);
        return $out;
    }
    
     public function get_product_pre_outlet_billed($lastedate_filter,$location,$pid) {
        global $dbc;
        $out = NULL;
        //$pre_mth = Date("Y-m", strtotime($date . " last month"));
        
       $q="select count(uso.retailer_id) AS pre_outlet_billed FROM retailer "
               . "INNER JOIN user_sales_order AS uso ON uso.retailer_id=retailer.id "
               . "INNER JOIN user_sales_order_details  AS usod ON usod.order_id= uso.order_id $lastedate_filter "
               . "AND uso.location_id=$location AND usod.product_id='$pid'";
       
       
      // h1($q); exit;
        list($opt, $rs) = run_query($dbc, $q, $mode = 'single', $msg = '');
        if (!$opt)
            return $out;
        return $rs['pre_outlet_billed'];
    }
    
    public function get_product_new_outlet_billed($rdatefilter,$location,$pid,$usodate) {
        global $dbc;
        $out = NULL;
        
        //$q="SELECT sum(co.amount) AS new_outlet_billed FROM retailer RIGHT JOIN challan_order AS co ON co.ch_retailer_id=retailer.id INNER JOIN challan_order_details AS cod ON cod.ch_id=co.id $filter_date AND co.ch_created_by=$id AND $filterret AND retailer.location_id=$location AND cod.product_id=$pid GROUP BY co.ch_created_by";
       $q="SELECT count(uso.retailer_id) AS new_outlet_billed FROM retailer INNER JOIN user_sales_order AS uso ON uso.retailer_id=retailer.id "
               . "INNER JOIN user_sales_order_details AS usod ON usod.order_id= uso.order_id $rdatefilter AND uso.location_id=$location AND usod.product_id='$pid' AND $usodate";
      // h1($q); exit;
        list($opt, $rs) = run_query($dbc, $q, $mode = 'single', $msg = '');
        if (!$opt)
            return $out;
        return $rs['new_outlet_billed'];
    } 
    
    public function get_product_total_outlet_billed($filter_date,$location,$pid) {
        global $dbc;
        $out = NULL;
        
       // $q="SELECT sum(co.amount) AS total_outlet_billed FROM retailer RIGHT JOIN challan_order AS co ON co.ch_retailer_id=retailer.id INNER JOIN challan_order_details AS cod ON cod.ch_id=co.id $filter_date AND co.ch_created_by=$id AND retailer.location_id=$location AND cod.product_id=$pid GROUP BY co.ch_created_by";
       $q="SELECT count(uso.retailer_id) AS total_outlet_billed FROM retailer INNER JOIN user_sales_order AS uso ON uso.retailer_id=retailer.id "
               . "INNER JOIN user_sales_order_details AS usod ON usod.order_id= uso.order_id $filter_date "
               . "AND uso.location_id=$location AND usod.product_id='$pid'";
       //h1($q); exit;
        list($opt, $rs) = run_query($dbc, $q, $mode = 'single', $msg = '');
        if (!$opt)
            return $out;
        return $rs['total_outlet_billed'];
    }
    
    /***************************************16th September 2017**************************************************************/
    
     public function get_dealer_sale_detail_list($filter = '', $records = '', $orderby = '',$csdate ='') {
        global $dbc;
        
        $filterstr = $this->oo_filter($filter, $records, $orderby); 
        $filterclaim = $this->oo_filter($csdate, $records, $orderby); 
        $out = array();
        
       // $q="SELECT sum(co.amount) AS total_outlet_billed FROM retailer RIGHT JOIN challan_order AS co ON co.ch_retailer_id=retailer.id INNER JOIN challan_order_details AS cod ON cod.ch_id=co.id $filter_date AND co.ch_created_by=$id AND retailer.location_id=$location AND cod.product_id=$pid GROUP BY co.ch_created_by";
       $q="SELECT distinct dealer.id,dealer.name,l4_name city,contact_person,other_numbers  FROM dealer INNER JOIN dealer_location_rate_list AS dlrl ON dlrl.dealer_id=dealer.id "
               . "INNER JOIN location_view AS lv ON lv.l5_id= dlrl.location_id $filterstr ";
      // h1($q);
        list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = '');
        if (!$opt)
            return $out;
        while($row=  mysqli_fetch_assoc($rs)){
            $id=$row['id'];
            $out[$id]['did']=$id;    
            $out[$id]['name']=$row['name'];            
            $out[$id]['city']=$row['city']; 
            $out[$id]['contact_person']=$row['contact_person']; 
            $out[$id]['other_numbers']=$row['other_numbers']; 
            $out[$id]['total_claim']= $this->get_dealer_claim_list($id,$filterclaim);
            $out[$id]['total_stock']= $this->get_dealer_total_stock_list($id);
          
        }
       // pre($out);
        return $out;
    }
    
  ###################################  
    public function get_dealer_claim_list($id,$filterclaim) {
           global $dbc;
           
        $q = "SELECT count(id) as total_claim FROM claim_challan $filterclaim AND dealer_id='$id'";
       //h1($q);
         list($opt, $rs) = run_query($dbc, $q, $mode = 'single', $msg = '');
        if (!$opt)
            return $out;
        return $rs['total_claim'];
    }
    
      public function get_dealer_total_stock_list($did)
        {
		global $dbc;
		$out = array();	
		
                $q1 = "SELECT sum(balance_stock + salable_stock) as total_stock FROM `dealer_available_stock` 
                    INNER JOIN catalog_view ON catalog_view.product_id=dealer_available_stock.pid 
                    where dealer_id='$did' ORDER BY catalog_view.c1_id";
              // h1($q1);
               // $product = array();
                list($opt, $rs) = run_query($dbc, $q1, $mode = 'single', $msg = '');
            if (!$opt)
                return $out;
            return $rs['total_stock'];
        }
    
        
      public function get_dealer_order_detail_list($filter = '', $records = '', $orderby = '',$id='') {
        global $dbc;
        
        $filterstr = $this->oo_filter($filter, $records, $orderby); 
        
        $out = array();
        
       // $q="SELECT sum(co.amount) AS total_outlet_billed FROM retailer RIGHT JOIN challan_order AS co ON co.ch_retailer_id=retailer.id INNER JOIN challan_order_details AS cod ON cod.ch_id=co.id $filter_date AND co.ch_created_by=$id AND retailer.location_id=$location AND cod.product_id=$pid GROUP BY co.ch_created_by";
       $q="SELECT uso.*,lv.l5_name as beat,DATE_FORMAT(date,'%d/%m/%Y') AS sale_date,"
               . "DATE_FORMAT(time,'%d/%m/%Y') AS sale_time,DATE_FORMAT(date,'%d/%b/%Y') AS fdated  "
               . "FROM user_sales_order uso INNER JOIN location_view lv ON lv.l5_id=uso.location_id $filterstr AND dealer_id='$id'";
     //  h1($q); exit;
        list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = '');
        if (!$opt)
            return $out;
        $retailer_map = get_my_reference_array('retailer', 'id', 'name');
        while($row=  mysqli_fetch_assoc($rs)){
            $id=$row['order_id'];
            $out[$id]=$row;
            $out[$id]['firm_name'] = $retailer_map[$row['retailer_id']];
            $out[$id]['person_name'] = $this->get_username($row['user_id']);
            $out[$id]['order_item'] = $this->get_my_reference_array_direct("SELECT usod.id,cp.name,cprl.base_price,usod.quantity,usod.scheme_qty,usod.product_id FROM user_sales_order_details usod INNER JOIN user_sales_order uso ON usod.order_id = uso.order_id INNER JOIN catalog_product cp ON usod.product_id=cp.id INNER JOIN catalog_2 c2 ON c2.id = cp.catalog_id  INNER JOIN person ON person.id = uso.user_id INNER JOIN state ON person.state_id = state.stateid INNER JOIN catalog_product_rate_list cprl ON usod.product_id = cprl.catalog_product_id AND state.stateid = cprl.stateId  WHERE user_id ='$row[user_id]' AND usod.order_id = $row[order_id]", 'id');
         
          
        }
     //   pre($out);
        return $out;
    }
    
     public function get_dealer_claim_detail_list($filter = '', $records = '', $orderby = '') {
           global $dbc;
           
        $filterstr = $this->oo_filter($filter, $records, $orderby); 
        
        $q = "SELECT *,DATE_FORMAT(from_date,'%d/%b/%Y') AS fdated,DATE_FORMAT(to_date,'%d/%b/%Y') AS tdated,DATE_FORMAT(claim_date,'%d/%b/%Y') AS cdated FROM claim_challan $filterstr";
       //h1($q);
         list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = '');
        if (!$opt)
            return $out;
         while($row=  mysqli_fetch_assoc($rs)){
            $id=$row['id'];
            $out[$id]=$row;
                     
        }
         return $out;
        
    }
    
      public function get_dealer_stock_detail_list($filter='',  $records = '', $orderby='')
	{
		global $dbc;
		$out = array();	
		$filterstr = $this->oo_filter($filter, $records, $orderby);
                $q1 = "SELECT pid,dealer_available_stock.salable_only as salable_only,dealer_available_stock.product_name as product_name,rate,balance_stock,salable_stock,non_salable_stock FROM `dealer_available_stock` 
                    INNER JOIN catalog_view ON catalog_view.product_id=dealer_available_stock.pid 
                    $filterstr ORDER BY catalog_view.c1_id";
              // h1($q1);
               // $product = array();
                list($opt1, $rs1) = run_query($dbc, $q1, $mode='multi', $msg='');
                if($opt1)
                {
                     while($row = mysqli_fetch_assoc($rs1)){
                            $id = $row['pid'];                        
                            $out[$id] = $row;	
                            $out[$id]['cat'] = $this->category($id); 
                           
                    }
                }
              
              // pre($out);
		return $out;
	}
        
        
          public function category($id)
	{
		global $dbc;
	        $q1 = " SELECT c1_name FROM  `catalog_view` WHERE product_id='$id'";
               // h1($q1);
                $outmg = mysqli_query($dbc, $q1);
                while($outr = mysqli_fetch_assoc($outmg))
                {
                   $out = $outr['c1_name'];
                }
              // print_r($out);
		return $out;
	}
   
   
    
    public function get_sale_order_list_report($filter = '', $records = '', $orderby = '') {
            global $dbc;
            $out = array();
            $filterstr = $this->oo_filter($filter, $records, $orderby);
            $mtype = $_SESSION[SESS . 'constant']['retailer_level'];

            $q = "SELECT *,location_2.name as state,location_5.name as beat,DATE_FORMAT(date,'%d/%m/%Y') AS sale_date,DATE_FORMAT(time,'%d/%m/%Y') AS sale_time,DATE_FORMAT(date,'%d/%b/%Y') AS fdated FROM user_sales_order "
                    . "INNER JOIN location_5 ON user_sales_order.location_id=location_5.id "
                    . "INNER JOIN location_4 ON location_5.location_4_id=location_4.id "
                    . "INNER JOIN location_3 ON location_4.location_3_id=location_3.id "
                    . "INNER JOIN location_2 ON location_3.location_2_id=location_2.id "
                    . "$filterstr";
            /* h1($q);*/
            list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = '');


            if (!$opt)
                return $out;
            $retailer_map = get_my_reference_array('retailer', 'id', 'name');
            $dealer_map = get_my_reference_array('dealer', 'id', 'name');
            /*pre($dealer_map);*/
            $brand_map = get_my_reference_array('catalog_1', 'id', 'name');

            /*echo "<pre>";print_r($rs);die();
            mysqli_result Object
            (
                [current_field] => 0
                [field_count] => 44
                [lengths] => 
                [num_rows] => 10
                [type] => 0
            )    
            */
            while ($row = mysqli_fetch_assoc($rs)) {
                $id = $row['order_id'];
                $out[$id] = $row; // storing the item id
                $out[$id]['name'] = $dealer_map[$row['dealer_id']];
                $out[$id]['firm_name'] = $retailer_map[$row['retailer_id']];
                $out[$id]['person_name'] = $this->get_username($row['user_id']);

                $out[$id]['order_item'] = $this->get_my_reference_array_direct("SELECT usod.id,cp.name,cprl.retailer_rate as base_price,usod.quantity,usod.scheme_qty,"
                        . "usod.product_id FROM user_sales_order_details usod INNER JOIN user_sales_order uso ON usod.order_id = uso.order_id  "
                        . "INNER JOIN catalog_product cp ON usod.product_id=cp.id INNER JOIN catalog_2 c2 ON c2.id = cp.catalog_id  "
                        . "INNER JOIN person ON person.id = uso.user_id INNER JOIN state ON person.state_id = state.stateid "
                        . "INNER JOIN product_rate_list cprl ON usod.product_id = cprl.product_id AND state.stateid = cprl.state_id  "
                        . "WHERE uso.user_id ='$row[user_id]' AND usod.order_id = $row[order_id]", 'id');

                $out[$id]['challan_item'] = $this->get_my_reference_array_direct("SELECT cod.id as ch_id,cod.order_id,cp.name,cprl.retailer_rate as base_price,cod.product_id as ch_product_id,cod.qty as ch_quantity,cod.free_qty as ch_scheme_qty "
                        . " FROM challan_order_details cod INNER JOIN catalog_product cp ON cod.product_id=cp.id  "
                        . " INNER JOIN catalog_2 c2 ON c2.id = cp.catalog_id "
                        . "INNER JOIN product_rate_list cprl ON cod.product_id = cprl.product_id  "
                        . "WHERE cod.order_id = $row[order_id] ", 'id');   


                $out[$id]['gift_item'] = $this->get_my_reference_array_direct("SELECT * FROM user_retailer_gift_details INNER JOIN _retailer_mkt_gift ON _retailer_mkt_gift.id = user_retailer_gift_details.gift_id WHERE order_id = '$row[order_id]'", 'id');
                $out[$id]['merch_item'] = $this->get_my_reference_array_direct("SELECT id,qty,`merchandise_name`,image from `merchandise` WHERE  `merchandise`.`order_id`='$id'", 'id');
            }// while($row = mysqli_fetch_assoc($rs)){ ends
           //pre($out);
            return $out;
        }
    
    public function get_retailer_stock_list($filter = '', $records = '', $orderby = '') {
        global $dbc;
        $out = array();
        $filterstr = $this->oo_filter($filter, $records, $orderby);
        $mtype = $_SESSION[SESS . 'constant']['retailer_level'];

        $q = "SELECT retailer_stock.*,l2_name as state,l5_name as beat,DATE_FORMAT(date,'%d/%m/%Y') AS sale_date FROM retailer_stock  "
                . "INNER JOIN location_view ON retailer_stock.location_id=location_view.l5_id "
                . "$filterstr";
        // h1($q);exit;
        list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = '');
        if (!$opt)
            return $out;
        $retailer_map = get_my_reference_array('retailer', 'id', 'name');
        $dealer_map = get_my_reference_array('dealer', 'id', 'name');
       // $brand_map = get_my_reference_array('catalog_1', 'id', 'name');
        while ($row = mysqli_fetch_assoc($rs)) {
            $id = $row['id'];
            $out[$id] = $row; // storing the item id
            $out[$id]['name'] = $dealer_map[$row['dealer_id']];
            $out[$id]['firm_name'] = $retailer_map[$row['retailer_id']];
            $out[$id]['person_name'] = $this->get_username($row['user_id']);
            $qrd="SELECT rsd.id AS id,cp.product_name AS name,cprl.retailer_rate as base_price,rsd.quantity,"
                    . "rsd.product_id FROM retailer_stock_details rsd 
                    INNER JOIN retailer_stock USING(order_id) "
                    . "INNER JOIN catalog_view cp ON rsd.product_id=cp.product_id "
                    . "INNER JOIN product_rate_list cprl ON rsd.product_id = cprl.product_id "
                    . "WHERE user_id ='$row[user_id]' AND rsd.order_id = $row[order_id] ";
            // $qrd="SELECT rsd.id AS id,cp.product_name AS name,rsd.quantity,"
            //         . "rsd.product_id FROM retailer_stock_details rsd "
            //         . "INNER JOIN catalog_view cp ON rsd.product_id=cp.product_id "
            //         . "WHERE rsd.user_id ='$row[user_id]' AND rsd.order_id = $row[order_id] ";
          // h1($qrd);
          $out[$id]['order_item'] = $this->get_my_reference_array_direct($qrd, 'id');


           // $out[$id]['gift_item'] = $this->get_my_reference_array_direct("SELECT * FROM user_retailer_gift_details INNER JOIN _retailer_mkt_gift ON _retailer_mkt_gift.id = user_retailer_gift_details.gift_id WHERE order_id = '$row[order_id]'", 'id');
           // $out[$id]['merch_item'] = $this->get_my_reference_array_direct("SELECT id,qty,`merchandise_name`,image from `merchandise` WHERE  `merchandise`.`order_id`='$id'", 'id');
        }// while($row = mysqli_fetch_assoc($rs)){ ends
       //pre($out);
        return $out;
    }

    public function get_product_wise_summary_list($filter='', $records = '', $orderby='')
        {

            global $dbc;
            $out = array(); 
            // if user has send some filter use them.
            $filterstr = $this->oo_filter($filter, $records, $orderby);
            //print_r($filterstr);
            $q="SELECT stock.id AS id,catalog_product.id as product_id,catalog_product.name AS product_name,stock.qty AS qty,mrp,(qty*dealer_rate) AS value FROM stock INNER JOIN catalog_product ON catalog_product.id=stock.product_id INNER JOIN dealer_person_login ON dealer_person_login.dealer_id=stock.dealer_id  $filterstr ORDER BY name ";

           // h1($q);
            list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
            if(!$opt) return $out; // if no order placed send blank array
            while($row = mysqli_fetch_assoc($rs))
            {
            $id = $row['id'];
            $product_id=$row['product_id'];
            $out[$id] = $row;
            }
            //pre($out);
            return $out;
            }
 public function get_product_billing_report_list($filter = '', $records = '', $orderby = '') {
global $dbc;
$filterstr = $this->oo_filter($filter, $records);
$out = NULL;

$q = "SELECT d.id as dealer_id,d.name AS rds_name,dealer_code,csa_id,csa_name AS csa, lv.l2_name AS state, lv.l4_name as town from dealer d INNER JOIN dealer_person_login as dpr ON dpr.dealer_id=d.id LEFT JOIN person_login ON person_login.person_username=dpr.uname INNER JOIN dealer_location_rate_list dlrl ON dlrl.dealer_id=d.id 
INNER JOIN location_view lv ON dlrl.location_id=lv.l5_id LEFT JOIN csa ON csa.c_id=d.csa_id 
$filterstr GROUP BY d.id order by lv.l2_name";
 //h1($q);
list($opt, $rs) = run_query($dbc, $q, 'multi');
if (!$opt)
return $out;
$i = 1;
$start = get_mysql_date($_POST['start'],'/',$time = false, $mysqlsearch = true);
$end = get_mysql_date($_POST['end'],'/',$time = false, $mysqlsearch = true);
while ($row = mysqli_fetch_assoc($rs)) {
$id = $i;
$dealer_id=$row[dealer_id];
$out[$id] = $row;
if(!empty($_POST['product_id'])){
  $pro=implode(',',$_POST['product_id']);
$product_id ="product_id IN($pro)";
}else{
$product_id ="product_id IN(0)";
}
//$cond="ch_dealer_id=".$dealer_id." AND DATE_FORMAT(`ch_date`,'%Y%m%d')>='".$start."' AND DATE_FORMAT(`ch_date`,'%Y%m%d')<='".$end."'";
$cond="ch_dealer_id=".$dealer_id." AND DATE_FORMAT(`ch_date`,'%Y%m%d')>='".$start."' AND DATE_FORMAT(`ch_date`,'%Y%m%d')<='".$end."' AND $product_id";
$out[$id][bill_cut]= myrowvaljoin('challan_order','count(ch_no)','challan_order_details','challan_order.id=challan_order_details.ch_id', $cond);
$out[$id][bill_qty]= myrowvaljoin('challan_order','sum(qty)','challan_order_details','challan_order.id=challan_order_details.ch_id', $cond);
$out[$id][billing_amount]= myrowvaljoin('challan_order','sum(product_rate*qty)','challan_order_details','challan_order.id=challan_order_details.ch_id', $cond);
$cond2="dealer_id=".$dealer_id." AND DATE_FORMAT(`date`,'%Y%m%d')>='".$start."' AND DATE_FORMAT(`date`,'%Y%m%d')<='".$end."' AND $product_id";
$out[$id][sale_order_value]= myrowvaljoin('user_sales_order_details','sum(rate*quantity)','user_sales_order','user_sales_order.order_id=user_sales_order_details.order_id',$cond2);
$cond2="dealer_id=".$dealer_id." AND DATE_FORMAT(`date`,'%Y%m%d')>='".$start."' AND DATE_FORMAT(`date`,'%Y%m%d')<='".$end."' AND $product_id";
$out[$id][sale_order_qty]= myrowvaljoin('user_sales_order_details','sum(quantity)','user_sales_order','user_sales_order.order_id=user_sales_order_details.order_id',$cond2);

$i++;
}
return $out;
}
  function get_oneDayReport_list($filter = '', $records = '', $orderby = '')
    {
         global $dbc;
         $out = array();
         $filterstr = $this->oo_filter($filter, $records, $orderby);
         $mtype = $_SESSION[SESS . 'constant']['retailer_level'];
         $start = get_mysql_date($_POST['start'],'/',$time = false, $mysqlsearch = true);
         $end = get_mysql_date($_POST['end'],'/',$time = false, $mysqlsearch = true);

         if(!empty($_POST['product_id'])){
          $pro=implode(',',$_POST['product_id']);
          $product_id ="product_id IN($pro)";
          }else{
            $product_id ="product_id IN(0)";
          }

         $q = "SELECT cv.product_name as sku,cv.c2_name as vname,uso.order_id AS order_id,user_id,retailer_id,l2_name as state, l2_id, l5_name as beat,DATE_FORMAT(date,'%d/%m/%Y') AS sale_date,DATE_FORMAT(time,'%d/%m/%Y') AS sale_time,DATE_FORMAT(date,'%d/%b/%Y') AS fdated,dealer.name AS name,dealer.csa_id 
         FROM user_sales_order uso 
         INNER JOIN user_sales_order_details usod ON uso.order_id=usod.order_id 
         INNER JOIN catalog_view cv ON usod.product_id=cv.product_id 
         INNER JOIN dealer ON dealer.id=uso.dealer_id  
         INNER JOIN location_view ON uso.location_id=l5_id "
                 . "$filterstr GROUP BY dealer.id,retailer_id ORDER BY state ASC";
         //h1($q);
         // die;
         // list($opt, $rs) = run_query($dbc, $q, $mode = 'multi', $msg = '');
          $rs = mysqli_query($dbc,$q);
         // if (!$opt)
         //     return $out;

          // h1('DEMO');
          // die;
       //  $retailer_map = get_my_reference_array('retailer', 'id', 'name');
       //  $dealer_map = get_my_reference_array('dealer', 'id', 'name');
        // $brand_map = get_my_reference_array('catalog_1', 'id', 'name');
         while ($row = mysqli_fetch_assoc($rs)) {
             $id = $row['order_id'];
             $out[$id] = $row; // storing the item id
           //  $out[$id]['name'] = $dealer_map[$row['dealer_id']];
             $out[$id]['firm_name'] = myrowval('retailer','name',"id=".$row['retailer_id']);
             $out[$id]['person_name'] = $this->get_username($row['user_id']);

             $cond2="retailer_id=".$row['retailer_id']." AND DATE_FORMAT(`date`,'%Y%m%d')>='".$start."' AND DATE_FORMAT(`date`,'%Y%m%d')<='".$end."' AND $product_id";
             $out[$id][sale_order_value]= myrowvaljoin('user_sales_order_details','sum(rate*quantity)','user_sales_order','user_sales_order.order_id=user_sales_order_details.order_id',$cond2);
            
             $out[$id][sale_order_qty]= myrowvaljoin('user_sales_order_details','sum(quantity)','user_sales_order','user_sales_order.order_id=user_sales_order_details.order_id',$cond2);
             $cond="ch_retailer_id=".$row['retailer_id']." AND DATE_FORMAT(`ch_date`,'%Y%m%d')>='".$start."' AND DATE_FORMAT(`ch_date`,'%Y%m%d')<='".$end."' AND $product_id";
            // $out[$id][bill_cut]= myrowvaljoin('challan_order','count(ch_no)','challan_order_details','challan_order.id=challan_order_details.ch_id', $cond);
             $out[$id][bill_qty]= myrowvaljoin('challan_order','sum(qty)','challan_order_details','challan_order.id=challan_order_details.ch_id', $cond);
            $out[$id][billing_amount]= myrowvaljoin('challan_order','sum(taxable_amt)','challan_order_details','challan_order.id=challan_order_details.ch_id', $cond);
             
         }// while($row = mysqli_fetch_assoc($rs)){ ends
        // pre($out);
        // die;
         return $out;
        
    }
    ###########################################################################
public function get_person_senior_details_list($filter='',  $records = '', $orderby='')
{
    global $dbc;
    $out = array();	
    $filterstr = $this->oo_filter($filter, $records, $orderby);
            $q1 = "SELECT  dealer.name as dealer_name , dlrl.dealer_id , p1.emp_code , p1.role_id as prole_id ,CONCAT_WS(' ',p1.first_name,p1.middle_name, p1.last_name) as user_name
            ,p1.head_quar as p_head_quart , p1.id as pid , p1.person_id_senior as senior_id ,
            (select CONCAT_WS(' ',person.first_name,person.middle_name, person.last_name)  from person where person.id=p1.person_id_senior ) as senior_name 
           , (select role_id from person where person.id=p1.person_id_senior ) as senior_role_id FROM person as p1  
            inner  join person_login as pl on pl.person_id=p1.id   
            left  join dealer_location_rate_list as dlrl on dlrl.user_id=p1.id 
            left join dealer on  dealer.id=dlrl.dealer_id
          left join location_view as lv on lv.l5_id=dlrl.location_id
          $filterstr    group by p1.id ,dlrl.dealer_id order by p1.first_name asc ,senior_name asc ";
        //  h1($q1);
           // $product = array();
            list($opt1, $rs1) = run_query($dbc, $q1, $mode='multi', $msg='');
            if($opt1)
            {   $i=1;
                 while($row = mysqli_fetch_assoc($rs1)){
                        $id = $row['pid'];
                        $id=$i;                        
                        $out[$id] = $row;	
          
          
              $out[$id]['user_beat'] = $this->get_user_beat($row['pid'] ,$row['dealer_id'] );   
              $i++; 
                }
            }
          
         //  pre($out);
    return $out;
}
/******/
public function get_user_beat($id ,$did ) {
    global $dbc;
$out=array();
    $q = "SELECT location_5.name as beat_name from location_5 inner join dealer_location_rate_list as dlrl on 
    dlrl.location_id=location_5.id where dlrl.user_id='$id' and dlrl.dealer_id='$did'  group by dlrl.location_id ";
// h1($q);
    list($opt, $rs1) = run_query($dbc, $q, $mode = 'multi', $msg = '');
    if ($opt)
    { 
        while($row = mysqli_fetch_assoc($rs1)){
            
             $out[] = $row['beat_name'];                        
        
    }
  //pre($out);
    $beat = implode("  , ", $out);
    
//h1( $beat);
}
        return $beat;
    
}
   


       
}

?>
