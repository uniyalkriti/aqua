<?php
class pulse_performance extends myfilter {
    
  

    public $id = NULL;
    public $id_detail = NULL;

    public function __construct($id = NULL) {
        parent::__construct();
        $this->id = $id;
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
    
    /*************customer-performance*******Start*******New REport 2nd Aug 2017************************/
  
    public function get_customer_performance_list($filter = '', $records = '', $orderby = '',$filterdate ='',$enddate = '',$filteruso='') {
    global $dbc;
    $out = array();

    $start = get_mysql_date($_POST['start'],'/',$time = false, $mysqlsearch = true);
    $end = get_mysql_date($_POST['end'],'/',$time = false, $mysqlsearch = true);
          
     $filterstr = $this->oo_filter($filter, $records, $orderby);  
     $filterlastm = $this->oo_filter($filterdate, $records, $orderby);  
     $filterenddate = $this->oo_filter($enddate, $records, $orderby);  
     $filteruson = $this->oo_filter($filteruso, $records, $orderby);  
   
            $q="SELECT person.product_division,person.id as pid,uso.dealer_id,uso.order_id as order_id,d.name as dealer_name,d.dealer_code,lv.l1_id AS stateid,lv.l2_name AS state,lv.l3_name AS city,uso.retailer_id AS rid,retailer.name AS ret_name,
                rot.outlet_type,CONCAT_WS(' ',first_name,middle_name,last_name) AS person_fullname,uso.date,person.role_id,person.emp_code,
                sum(rate*quantity) as order_value_booked,sum(quantity) as order_quantity_booked
               
                from user_sales_order uso INNER JOIN user_sales_order_details usod using(order_id) "
                
                . " INNER JOIN dealer d ON d.id=uso.dealer_id "
                . " LEFT JOIN retailer ON retailer.id=uso.retailer_id "
                . " LEFT JOIN _retailer_outlet_type rot ON rot.id= retailer.outlet_type_id "
                . " INNER JOIN location_view AS lv ON lv.l5_id = uso.location_id "                
                . " INNER JOIN person ON person.id=uso.user_id "           
                . " $filterstr AND person.role_id in (2,3,10,14,15,21,30,31,32,33) AND usod.product_id=164 GROUP BY uso.retailer_id,person.id";
            
        //h1($q); exit;
    $rs = mysqli_query($dbc, $q); 
    $inc=1;     
    //if(!$opt) return $out; // if no order placed send blank array
    while ($row = mysqli_fetch_assoc($rs)) {
        $id = $row['rid'];
        $id=$inc;
        $out[$id] = $row;
        $out[$id]['lm_order_value']= $this->get_lm_order_value_booked($id,$filterlastm,$row['pid']);
        $out[$id]['giving_value']= $this->get_given_value_till_booked($id,$filterenddate,$row['pid']);
        $out[$id]['total_call']= $this->get_total_call($id,$filteruson,$row['pid']);
        $out[$id]['pro_call']= $this->get_pro_call($id,$filteruson,$row['pid']);
        $out[$id]['mm_order']= $this->get_mm_order_booked($id,$filterlastm,$row['pid']);
       $out[$id]['hing_order']= $this->get_hing_order_booked($id,$filterlastm,$row['pid']);
        $out[$id]['designation']= myrowval('_role','rolename','role_id='.$row['role_id']);

        $where1 = "user_id=".$row['pid']." AND DATE_FORMAT(date,'%Y%m%d')>='$start' AND DATE_FORMAT(date,'%Y%m%d')<='$end' AND retailer_id=".$row['rid'];
        $out[$id]['contacted']= myrowval('user_sales_order','count(call_status)',$where1);

        $where2 = "user_id=".$row['pid']." AND DATE_FORMAT(date,'%Y%m%d')>='$start' AND DATE_FORMAT(date,'%Y%m%d')<='$end' AND retailer_id=".$row['rid']." and call_status=1";
        $out[$id]['booked']= myrowval('user_sales_order','count(call_status)',$where2);
        $inc++;
    }  
    //pre($out);
    return $out;
}

    public function get_rnd_vs_billing_list($filter = '', $records = '', $orderby = '',$filterdate ='',$enddate = '',$filteruso='') {
    global $dbc;
    $out = array();

    $start = get_mysql_date($_POST['start'],'/',$time = false, $mysqlsearch = true);
    $end = get_mysql_date($_POST['end'],'/',$time = false, $mysqlsearch = true);
          
     $filterstr = $this->oo_filter($filter, $records, $orderby);  
     $filterlastm = $this->oo_filter($filterdate, $records, $orderby);  
     $filterenddate = $this->oo_filter($enddate, $records, $orderby);  
     $filteruson = $this->oo_filter($filteruso, $records, $orderby);
            
            $q="SELECT person.product_division,person.id as pid,retailer.dealer_id,d.name as dealer_name,d.dealer_code,lv.l2_id as stateid,lv.l2_name AS state,lv.l4_name AS city,retailer.id AS rid,retailer.name AS ret_name,CONCAT_WS(' ',first_name,middle_name,last_name) AS person_fullname,person.role_id,person.emp_code,retailer.retailer_status
                from retailer  "
                . " INNER JOIN dealer d ON d.id=retailer.dealer_id "
                . " INNER JOIN location_view AS lv ON lv.l5_id = retailer.location_id " 
                . " INNER JOIN user_dealer_retailer udr ON udr.retailer_id=retailer.id"               
                . " INNER JOIN person ON person.id=udr.user_id "           
                . " $filterstr AND person.role_id in (3,10,14,15,21,30,31,32,33) GROUP BY lv.l2_id,rid,retailer.dealer_id,person.id";
           //h1($q); //exit;

    $rs = mysqli_query($dbc, $q);
    //$i =1;     
    //if(!$opt) return $out; // if no order placed send blank array
    while ($row = mysqli_fetch_assoc($rs)) {
        $id = $row['rid'];
        $i=$row['rid'];

        $out[$i] = $row;
        
        $out[$i]['designation']= myrowval('_role','rolename','role_id='.$row['role_id']);
        

        $where1 = "user_id=".$row['pid']." AND DATE_FORMAT(date,'%Y%m%d')>='$start' AND DATE_FORMAT(date,'%Y%m%d')<='$end' AND retailer_id=".$row['rid']." AND dealer_id=".$row['dealer_id']." and call_status=1 AND product_id=164";

        $out[$i]['booked']= myrowvaljoin('user_sales_order','count(user_sales_order.order_id)','user_sales_order_details','user_sales_order.order_id=user_sales_order_details.order_id',$where1);


        $where2 = "ch_user_id=".$row['pid']." AND DATE_FORMAT(ch_date,'%Y%m%d')>='$start' AND DATE_FORMAT(ch_date,'%Y%m%d')<='$end' AND ch_retailer_id=".$row['rid']." AND ch_dealer_id=".$row['dealer_id']  ." AND product_id=164";
        
        $out[$i]['billed']= myrowvaljoin('challan_order','count(ch_no)','challan_order_details','challan_order.id=challan_order_details.ch_id',$where2);

        $out[$i]['booked_qty']= myrowvaljoin('user_sales_order','SUM(quantity)','user_sales_order_details','user_sales_order.order_id=user_sales_order_details.order_id',$where1);

        $out[$i]['billed_qty']= myrowvaljoin('challan_order','SUM(qty)','challan_order_details','challan_order.id=challan_order_details.ch_id',$where2);

        $out[$i]['booked_value']= myrowvaljoin('user_sales_order','SUM(amount)','user_sales_order_details','user_sales_order.order_id=user_sales_order_details.order_id',$where1);
        $out[$i]['billed_value']= myrowvaljoin('challan_order','SUM(amount)','challan_order_details','challan_order.id=challan_order_details.ch_id',$where2);
        


      // $i++;
    }  
   //pre($out);exit;
    return $out;
} 


    public function get_mm_order_booked($id,$filterlastm,$pid) {
            global $dbc;
            $out = NULL;
            
            $q="SELECT sum(quantity) AS mm_order "
                       . "FROM user_sales_order AS uso INNER JOIN user_sales_order_details usod using(order_id) "
                    . "INNER JOIN catalog_product ON catalog_product.id=usod.product_id "
                       . " $filterlastm AND uso.user_id='$pid' AND uso.retailer_id='$id' AND catalog_product.division='1' "
                      . " GROUP BY uso.retailer_id,uso.user_id";                
            //h1($q);
            list($opt, $rs) = run_query($dbc, $q, $mode = 'single', $msg = '');
            if (!$opt)
                return $out;
            return $rs['mm_order'];
        }
        public function get_hing_order_booked($id,$filterlastm,$pid) {
            global $dbc;
            $out = NULL;
            
             $q="SELECT sum(quantity) AS hing_order "
                       . "FROM user_sales_order AS uso INNER JOIN user_sales_order_details usod using(order_id) "
                    . "INNER JOIN catalog_view ON catalog_view.product_id=usod.product_id "
                       . " $filterlastm AND uso.user_id='$pid' AND uso.retailer_id='$id' AND c1_id='120150507011301' "
                      . " GROUP BY uso.retailer_id,uso.user_id";                
            //h1($q);
            list($opt, $rs) = run_query($dbc, $q, $mode = 'single', $msg = '');
            if (!$opt)
                return $out;
            return $rs['hing_order'];
        }
    
    public function get_lm_order_value_booked($id,$filterlastm,$pid) {
        global $dbc;
        $out = NULL;
        
        $q="SELECT sum(rate*quantity) AS lmo_value "
          . " FROM user_sales_order AS uso INNER JOIN user_sales_order_details usod using(order_id) "
          . " $filterlastm AND uso.user_id='$pid' AND uso.retailer_id='$id' "
          . " GROUP BY uso.retailer_id,uso.user_id";                
        //h1($q);
        list($opt, $rs) = run_query($dbc, $q, $mode = 'single', $msg = '');
        if (!$opt)
            return $out;
        return $rs['lmo_value'];
    } 
    
      public function get_total_call($id,$filteruson,$pid) {
        global $dbc;
        $out = NULL;
        
        $q="select count(order_id) as total_call from user_sales_order us $filteruson AND us.retailer_id='$id' AND us.user_id='$pid'";                
       // h1($q);
        list($opt, $rs) = run_query($dbc, $q, $mode = 'single', $msg = '');
        if (!$opt)
            return $out;
        return $rs['total_call'];
    } 
    
      public function get_pro_call($id,$filteruson,$pid) {
        global $dbc;
        $out = NULL;
        
        $q="select count(order_id) as pro_call from user_sales_order us $filteruson AND us.retailer_id='$id' AND us.user_id='$pid' AND call_status='1'";                
       // h1($q);
        list($opt, $rs) = run_query($dbc, $q, $mode = 'single', $msg = '');
        if (!$opt)
            return $out;
        return $rs['pro_call'];
    } 
    
    public function get_given_value_till_booked($id,$enddate,$pid) {
        global $dbc;
        $out = NULL;
        
        $q="SELECT sum(rate*quantity) AS giving_value "
                   . "FROM user_sales_order AS uso INNER JOIN user_sales_order_details usod using(order_id) "
                   . " $enddate AND uso.user_id='$pid' AND uso.retailer_id='$id' AND usod.product_id=164"
                  . " GROUP BY uso.retailer_id,uso.user_id";                
       // h1($q);
        list($opt, $rs) = run_query($dbc, $q, $mode = 'single', $msg = '');
        if (!$opt)
            return $out;
        return $rs['giving_value'];
    } 
    

    
      public function get_productive_outlet_tracking_list($filter = '', $records = '', $orderby = '',$date_filter ='') {
        global $dbc;
        $out = array();
        $uid = $_SESSION[SESS . 'data']['id'];
        
         $filterstr = $this->oo_filter($filter, $records, $orderby);  
         $datefilter = $this->oo_filter($date_filter, $records, $orderby); 
         
        $del="DELETE FROM `_temp_outlet_repeat_order` WHERE user_id='$uid'";
        $rdel = mysqli_query($dbc, $del);
         
         $insert = "insert into _temp_outlet_repeat_order(user_id,pid,retailer_id,total_call)"
                 . "(select $uid,person.id,udr.retailer_id,(select count(uso.order_id) from user_sales_order uso INNER JOIN user_sales_order_details usod USING(order_id) 
                     $datefilter AND uso.user_id=person.id and uso.retailer_id=udr.retailer_id and call_status='1' AND usod.product_id=164) as total_call FROM person 
                    INNER JOIN person_login ON person_login.person_id = person.id AND person_status ='1' 
                    INNER JOIN state ON state.stateid=person.state_id 
                    INNER JOIN user_dealer_retailer udr ON udr.user_id=person.id "
                 . " $filterstr AND person.id != 1  "
                 . "order by state.stateid,person.id)"; 
                 
        $rdr = mysqli_query($dbc, $insert);
        
         $up = "update _temp_outlet_repeat_order temp set temp.repeat_order= 1 where temp.total_call > 1 and temp.user_id='$uid'";
     
             $r1 = mysqli_query($dbc, $up);
       
        $q="SELECT person.product_division,person.id AS pid,CONCAT_WS(' ',first_name, middle_name, last_name) AS person_fullname, 
                    (select CONCAT_WS(' ',first_name, middle_name, last_name)  as senior 
                    from person p where p.id=person.person_id_senior) as senior_person_name,person.person_id_senior,
                    person.state_id,state.statename AS state FROM person  
                     INNER JOIN person_login ON person_login.person_id = person.id AND person_status ='1'   
                     INNER JOIN state ON state.stateid=person.state_id 
                     $filterstr AND person.id != 1 order by state.stateid,person.person_id_senior,pid";
                
           //     h1($q); //exit;
        $rs = mysqli_query($dbc, $q);    
        while ($row = mysqli_fetch_assoc($rs)) {
          
            $id = $row['pid'];
            $out[$id] = $row;
            $out[$id]['total_retailers'] = $this->get_total_retailer_area($row['pid']);  
            $out[$id]['total_outlet_visit'] = $this->get_total_outlet_visit($row['pid'],$datefilter);  
            $out[$id]['total_outlet_billed'] = $this->get_total_outlet_pro($row['pid'],$datefilter); 
            $out[$id]['total_outlet_visit_pro'] = $this->get_total_outlet_visit_pro($row['pid'],$datefilter);           
            $out[$id]['total_repeat_orders'] = $this->get_total_outlet_repeat_orders($row['pid'],$uid);
           
          //  $out[$id]['so']= $this->get_so($id);
        }           
    // pre($out); exit;
        return $out;
    } 
    
    public function get_total_outlet_repeat_orders($pid,$uid) {
        global $dbc;        
        $out = NULL;
        $q = "SELECT sum(repeat_order) AS repeat_orders FROM _temp_outlet_repeat_order WHERE user_id='$uid' and pid='$pid' GROUP BY pid";
        // h1($q);
        list($opt, $rs) = run_query($dbc, $q, 'single');
        if (!$opt)
            return $out;
        return $rs['repeat_orders'];
        
        
    }
    
     public function get_total_retailer_area($user_id) {
        global $dbc;
        $out = NULL;
        $q = "SELECT COUNT(id) AS total FROM retailer INNER JOIN user_dealer_retailer udr ON udr.retailer_id=retailer.id "
                . "WHERE udr.user_id='$user_id' GROUP BY udr.user_id";
        // h1($q);
        list($opt, $rs) = run_query($dbc, $q, 'single');
        if (!$opt)
            return $out;
        return $rs['total'];
    }
    
    
     public function get_total_outlet_visit($user_id,$datefilter) {
           global $dbc;
           
        $q = "SELECT count(distinct retailer_id) as total_visit FROM user_sales_order uso INNER JOIN user_sales_order_details usod USING(order_id) $datefilter AND user_id='$user_id' AND usod.product_id=164";
      // h1($q);
         list($opt, $rs) = run_query($dbc, $q, $mode = 'single', $msg = '');
        if (!$opt)
            return $out;
        return $rs['total_visit'];
    }
    
     public function get_total_outlet_pro($user_id,$datefilter) {
           global $dbc;
           
        $q = "SELECT count(retailer_id) as total_billed FROM user_sales_order uso INNER JOIN user_sales_order_details usod USING(order_id) $datefilter AND uso.user_id='$user_id' AND uso.call_status='1' AND usod.product_id=164";
       //h1($q);
         list($opt, $rs) = run_query($dbc, $q, $mode = 'single', $msg = '');
        if (!$opt)
            return $out;
        return $rs['total_billed'];
    }
    
      public function get_total_outlet_visit_pro($user_id,$datefilter) {
           global $dbc;
           
        $q = "SELECT count(distinct retailer_id) as total_visit_pro FROM user_sales_order uso INNER JOIN user_sales_order_details usod USING(order_id) $datefilter AND uso.user_id='$user_id' AND usod.product_id=164 AND call_status='1'";
      // h1($q);
         list($opt, $rs) = run_query($dbc, $q, $mode = 'single', $msg = '');
        if (!$opt)
            return $out;
        return $rs['total_visit_pro'];
    }
    
       public function get_user_performance_list($filter = '', $records = '', $orderby = '',$filterdate ='') {
        global $dbc;
        $out = array();
              
         $filterstr = $this->oo_filter($filter, $records, $orderby);  
        
       
                // $q="select uda.id AS idd,person.id as user_id,ws.name as attendance,date_format(work_date,'%d-%m-%Y') as att_date,
                //     date_format(work_date,'%Y-%m-%d') as order_date,
                //     CONCAT_WS(' ',first_name,middle_name,last_name) AS person_name,person.emp_code,
                //     dealer.name as dealer_name,dealer.id as dealer_id,  
                //     lv.l4_name as town,_role.rolename as designation from user_daily_attendance uda 
                //     INNER JOIN _working_status ws ON ws.id=uda.work_status
                //     INNER JOIN person ON person.id = uda.user_id 
                //     INNER JOIN _role ON _role.role_id = person.role_id
                //     INNER JOIN dealer_location_rate_list uso ON uso.user_id=uda.user_id
                //     INNER JOIN location_view lv ON lv.l5_id=uso.location_id
                //     INNER JOIN dealer ON dealer.id=uso.dealer_id $filterstr 
                //     group by att_date,user_id,dealer_id order by user_id,att_date asc";
     $q="select uda.order_id AS idd,person.id as user_id,person_id_senior as senior_id,date,date_format(date,'%d-%m-%Y') as att_date,SUM(rate*quantity) as order_value,lv.l2_name as state,
                   COUNT(distinct order_id) as total_call, CONCAT_WS(' ',first_name,middle_name,last_name) AS person_name,person.emp_code,
                    dealer.name as dealer_name,dealer.id as dealer_id,  
                    lv.l4_name as town,_role.rolename as designation from user_sales_order uda 
                    INNER JOIN user_sales_order_details USING(order_id)
                    INNER JOIN person ON person.id = uda.user_id 
                    INNER JOIN _role ON _role.role_id = person.role_id
                    INNER JOIN location_view lv ON lv.l5_id=uda.location_id
                    INNER JOIN dealer ON dealer.id=uda.dealer_id $filterstr  
                    group by att_date,user_id,dealer_id order by user_id,att_date asc";                
             // h1($q); exit;
        $inc = 1;
        $rs = mysqli_query($dbc, $q);           
        while ($row = mysqli_fetch_assoc($rs)) {
            $id = $row['idd'];
            $out[$inc] = $row;
           
            $out[$inc]['attendance']= $this->get_attendance_details($row['date'],$row['user_id']);
            $out[$inc]['pro_call']= $this->get_user_pro_call($row['date'],$row['user_id'],$row['dealer_id']);
            $out[$inc]['new_outlet_visit'] = $this->get_user_new_outlet_visit($row['date'],$row['user_id'],$row['dealer_id']);
            $out[$inc]['new_outlet_details'] = $this->get_user_new_outlet_billed($row['date'],$row['user_id'],$row['dealer_id']);
            $out[$inc]['senior'] = myrowval('person',"CONCAT_WS(' ',first_name,middle_name,last_name)",'id='.$row['senior_id']);

         // $out[$inc]['new_outlet_value'] = $this->get_user_order_outlet_value($row['date'],$row['user_id'],$row['dealer_id']);            
           // $out[$id]['order_value']= $this->get_user_order_value($row['order_date'],$row['user_id'],$row['dealer_id']);
            
            $inc++;
        }  
        //pre($out);
        return $out;
    }
    public function get_attendance_details($date,$user_id) {
        global $dbc;
        $out = NULL;
        
        $q="select ws.name as work_status,DATE_FORMAT(`work_date`,'%H:%i:%s') as att_time FROM user_daily_attendance uda INNER JOIN  _working_status ws ON ws.id=uda.work_status WHERE user_id='$user_id' AND DATE_FORMAT(`work_date`,'%Y-%m-%d')='$date' ";                
       // h1($q);
        list($opt, $rs) = run_query($dbc, $q, $mode = 'single', $msg = '');
        if (!$opt)
            return $out;
        
         return $rs;
    } 
    public function get_user_total_call($date,$user_id,$dealer_id) {
        global $dbc;
        $out = NULL;
        
        $q="select count(order_id) as total_call from user_sales_order us where us.date = '$date' AND us.user_id='$user_id' AND us.dealer_id='$dealer_id'";                
       // h1($q);
        list($opt, $rs) = run_query($dbc, $q, $mode = 'single', $msg = '');
        if (!$opt)
            return $out;
        return $rs['total_call'];
    } 
    
      public function get_user_pro_call($date,$user_id,$dealer_id) {
        global $dbc;
        $out = NULL;
        
        $q="select count(DISTINCT order_id) as pro_call from user_sales_order us where us.date = '$date' AND us.user_id='$user_id' AND us.dealer_id='$dealer_id' AND call_status='1'";                
       // h1($q);
        list($opt, $rs) = run_query($dbc, $q, $mode = 'single', $msg = '');
        if (!$opt)
            return $out;
        return $rs['pro_call'];
    }
    
    
    public function get_user_order_value($date,$user_id,$dealer_id) {
        global $dbc;
        $out = NULL;
        
        $q="SELECT sum(rate*quantity) AS o_value "
                   . "FROM user_sales_order AS uso INNER JOIN user_sales_order_details usod using(order_id) "
                   . " where uso.date = '$date' AND uso.user_id='$user_id' AND uso.dealer_id='$dealer_id' "
                    . " GROUP BY uso.date,uso.user_id,uso.dealer_id";                
       // h1($q);
        list($opt, $rs) = run_query($dbc, $q, $mode = 'single', $msg = '');
        if (!$opt)
            return $out;
        return $rs['o_value'];
    } 
    
     public function get_user_new_outlet_visit($date,$user_id,$dealer_id) {
           global $dbc;
           
        $q = "SELECT count(distinct retailer.id) as new_outlet_visit FROM retailer  "
              . " where created_by_person_id='$user_id'  AND dealer_id='$dealer_id' "
		."AND date_format(retailer.created_on,'%Y-%m-%d')='$date'";
       //h1($q); die;
         list($opt, $rs) = run_query($dbc, $q, $mode = 'single', $msg = '');
        if (!$opt)
            return $out;
        return $rs['new_outlet_visit'];
    }
    
     public function get_user_new_outlet_billed($date,$user_id,$dealer_id) {
           global $dbc;
          //  $out = NULL;
        $q = "SELECT count(distinct retailer_id) as new_outlet_billed,SUM(rate*quantity) as sale_value FROM retailer "
              . "INNER JOIN user_sales_order AS uso ON uso.retailer_id=retailer.id INNER JOIN user_sales_order_details usod using(order_id) where uso.user_id='$user_id' AND uso.date='$date' AND uso.dealer_id='$dealer_id' "
		."AND date_format(retailer.created_on,'%Y-%m-%d')='$date' and uso.call_status='1'";
      // h1($q); die;
         list($opt, $rs) = run_query($dbc, $q, $mode = 'single', $msg = '');
        if (!$opt)
            return $out;
        return $rs['new_outlet_billed'].'||'.$rs['sale_value'];
    }
    
      public function get_user_order_outlet_value($date,$user_id,$dealer_id) {
           global $dbc;
           
        $q = "SELECT sum(rate*quantity) AS new_outlet_value FROM retailer "
              . "INNER JOIN user_sales_order AS uso ON uso.retailer_id=retailer.id "
              . "INNER JOIN user_sales_order_details usod using(order_id) "
              . "where uso.user_id='$user_id' AND uso.date='$date' AND uso.dealer_id='$dealer_id' "
		."AND date_format(retailer.created_on,'%Y-%m-%d')='$date' and uso.call_status='1' GROUP BY uso.date,uso.user_id,uso.dealer_id";
       //h1($q); die;
         list($opt, $rs) = run_query($dbc, $q, $mode = 'single', $msg = '');
        if (!$opt)
            return $out;
        return $rs['new_outlet_value'];
    }

    public function get_product_user_performance_list($filter = '', $records = '', $orderby = '',$filterdate ='') {
        global $dbc;
        $out = array();
              
         $filterstr = $this->oo_filter($filter, $records, $orderby);  
     $q="select uda.order_id AS idd,person.id as user_id,person_id_senior as senior_id,date,date_format(date,'%d-%m-%Y') as att_date,SUM(rate*quantity) as order_value,lv.l2_name as state,
                   COUNT(distinct order_id) as total_call, CONCAT_WS(' ',first_name,middle_name,last_name) AS person_name,person.emp_code,
                    dealer.name as dealer_name,dealer.id as dealer_id,  
                    lv.l4_name as town,_role.rolename as designation from user_sales_order uda 
                    INNER JOIN user_sales_order_details USING(order_id)
                    INNER JOIN person ON person.id = uda.user_id 
                    INNER JOIN _role ON _role.role_id = person.role_id
                    INNER JOIN location_view lv ON lv.l5_id=uda.location_id
                    INNER JOIN dealer ON dealer.id=uda.dealer_id $filterstr  
                    group by att_date,user_id,dealer_id order by user_id,att_date asc";                
             //h1($q); exit;
        $inc = 1;
        $rs = mysqli_query($dbc, $q); 
        $product_id=$_POST['product_id']; 
        $product_id=implode(',',$product_id);         
        while ($row = mysqli_fetch_assoc($rs)) {
            $id = $row['idd'];
            $out[$inc] = $row;
           
            $out[$inc]['attendance']= $this->get_attendance_details($row['date'],$row['user_id']);
            $out[$inc]['pro_call']= $this->get_user_product_call($row['date'],$row['user_id'],$row['dealer_id'],$product_id);
            $out[$inc]['new_outlet_visit'] = $this->get_user_new_outlet_visit($row['date'],$row['user_id'],$row['dealer_id']);
            $out[$inc]['new_outlet_details'] = $this->get_product_user_new_outlet_billed($row['date'],$row['user_id'],$row['dealer_id'],$product_id);
            $out[$inc]['senior'] = myrowval('person',"CONCAT_WS(' ',first_name,middle_name,last_name)",'id='.$row['senior_id']);

          //$out[$inc]['new_outlet_value'] = $this->get_user_order_outlet_value($row['date'],$row['user_id'],$row['dealer_id']);            
           // $out[$id]['order_value']= $this->get_user_order_value($row['order_date'],$row['user_id'],$row['dealer_id']);
            
            $inc++;
        }  
        //pre($out);
        return $out;
        } 
    public function get_user_product_call($date,$user_id,$dealer_id,$product_id) {
        global $dbc;
        $out = NULL;
        
        $q="select count(DISTINCT order_id) as pro_call from user_sales_order us INNER JOIN user_sales_order_details USING(order_id) where us.date = '$date' AND us.user_id='$user_id' AND us.dealer_id='$dealer_id' AND call_status='1' AND product_id IN($product_id)";                
        //h1($q);
        list($opt, $rs) = run_query($dbc, $q, $mode = 'single', $msg = '');
        if (!$opt)
            return $out;
        return $rs['pro_call'];
    }
    public function get_product_user_new_outlet_billed($date,$user_id,$dealer_id,$product_id) {
           global $dbc;
           
          //  $out = NULL;
        $q = "SELECT count(distinct retailer_id) as new_outlet_billed,SUM(rate*quantity) as sale_value FROM retailer "
              . "INNER JOIN user_sales_order AS uso ON uso.retailer_id=retailer.id INNER JOIN user_sales_order_details usod using(order_id) where uso.user_id='$user_id' AND uso.date='$date' AND uso.dealer_id='$dealer_id' AND usod.product_id IN($product_id) "
    ."AND date_format(retailer.created_on,'%Y-%m-%d')='$date' and uso.call_status='1'";
      // h1($q); die;
         list($opt, $rs) = run_query($dbc, $q, $mode = 'single', $msg = '');
        if (!$opt)
            return $out;
        return $rs['new_outlet_billed'].'||'.$rs['sale_value'];
    }
    
}

?>