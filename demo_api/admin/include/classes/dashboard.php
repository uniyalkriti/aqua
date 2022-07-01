<?php
class dashboard extends myfilter {

    public $id = NULL;
    public $id_detail = NULL;

    public function __construct($id = NULL) {
        parent::__construct();
        $this->id = $id;
    }

   ################################################################
   public function get_dashboard_report_list($filter4='',  $records = '', $orderby='')
	{
		global $dbc;
		$out = array();		
		// if user has send some filter use them.
        $filterstr = $this->oo_filter($filter4, $records, $orderby);
        $start = get_mysql_date($_POST['start'],'/',$time = false, $mysqlsearch = true);
 
 $q="SELECT CONCAT_WS(' ',first_name, middle_name, last_name) AS username ,_role.rolename as role_name ,
 person.id as pid FROM `person` inner join person_login on person_login.person_id=person.id 
 inner join _role on _role.role_id=person.role_id
  $filterstr   group by person.id  ";
 
     //     $q = "SELECT CONCAT_WS(' ',first_name, middle_name, last_name) AS username ,
    //      person.id as pid ,
    //      date_format(user_daily_attendance.work_date,'%d-%m-%y') as checkdate ,
    //      date_format(user_daily_attendance.work_date,'%h:%i:%s') as check_intime , 
    //      user_daily_attendance.remarks as inremarks,check_out.remarks as outremarks,
    //       date_format(check_out.work_date,'%h:%i:%s') as check_outtime
    //          FROM `person`
    //     left join check_out on check_out.user_id=person.id
    //     left join user_daily_attendance on person.id=user_daily_attendance.user_id 
    //      inner join person_login on person_login.person_id=person.id  $filterstr group by person.id";
     // h1($q);
		list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
        if(!$opt) return $out; // if no order placed send blank array
        
		while($row = mysqli_fetch_assoc($rs))
		{
           
			$id = $row['pid'];
			$out[$id] = $row;
            $out[$id]['user_beat'] = $this->get_user_beat($row['pid'] ,$start);
            $beat1=explode('|',$out[$id]['user_beat']) ;
           // h1($beat1[1]);
            //$out[$id]['dealername'] = $this->get_dealer_deatils($row['pid'] ,$start);
            $x = $this->get_dealer_deatils($row['pid'] ,$start);
            $dealer=explode('|',$x) ;
            $out[$id]['dealername'] = $dealer[0];
            $out[$id]['retailer_count'] = $this->get_user_dealer_retailer_count($row['pid'],$beat1[1],$dealer[1]);
            $out[$id]['attendance'] = $this->get_userattendance($row['pid'],$start);
            $out[$id]['checkoutattendance'] = $this->get_usercheckattendance($row['pid'],$start);
                           
        }
       
		return $out;
    } 
    

     ###########################################3
     public function get_usercheckattendance($uid ,$start)
     {
         global $dbc;
         $out = array();		
         // if user has send some filter use them.
        // $filterstr = $this->oo_filter($uid,$filter, $records, $orderby);
         //h1($filter);
         $q = "SELECT user_id, check_out.remarks as outremarks, date_format(check_out.work_date,'%h:%i:%s')
          as check_outtime  from check_out where user_id='$uid'
         and date_format(check_out.work_date,'%Y%m%d')='$start'  ";
     // h1($q);
         list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
         if(!$opt) return $out; // if no order placed send blank array
         while($row = mysqli_fetch_assoc($rs))
         {
              $id = $row['user_id'];
              $out = $row;
 
         }
            
                         
         
         return $out;
     } 
    ###########################################3
    public function get_userattendance($uid, $start)
    {
        global $dbc;
        $out = array();		
        // if user has send some filter use them.
       // $filterstr = $this->oo_filter($uid,$filter, $records, $orderby);
        //h1($filter);
        $q = "SELECT user_id , date_format(user_daily_attendance.work_date,'%d-%m-%y') as checkdate ,
            date_format(user_daily_attendance.work_date,'%h:%i:%s') as check_intime , 
          user_daily_attendance.remarks as inremarks from user_daily_attendance 
          where user_id='$uid'  and date_format(user_daily_attendance.work_date,'%Y%m%d')='$start'";
    //h1($q);
        list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
        if(!$opt) return $out; // if no order placed send blank array
        while($row = mysqli_fetch_assoc($rs))
        {
             $id = $row['user_id'];
             $out = $row;

        }
           
                        
        
        return $out;
    } 
    ##########################################################
    ##################################
    public function get_user_productivecall_count($uid ,$filter,  $records = '', $orderby='' )
    {
        global $dbc;
        $out = array();		
        // if user has send some filter use them.
        //$filterstr = $this->oo_filter($uid,$filter1, $records, $orderby);
        //h1($filter);
        $q = "SELECT count(call_status) as productivecall FROM `user_sales_order` where user_id='$uid'
       and call_status='1' and date_format(date,'%Y%m%d')='$filter'";
    //h1($q);
        list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
        if(!$opt) return $out; // if no order placed send blank array
        $row = mysqli_fetch_assoc($rs);
           // $id = $row['user_id'];
            $out = $row['productivecall'];
                        
        
        return $out;
    } 
   ###################################### 
   public function get_user_total_count($uid ,$filter,  $records = '', $orderby='' )
   {
       global $dbc;
       $out = array();		
       // if user has send some filter use them.
       //$filterstr = $this->oo_filter($uid,$filter1, $records, $orderby);
       //h1($filter);
       $q = "SELECT count(call_status) as totalcall FROM `user_sales_order` where user_id='$uid'
       and date_format(date,'%Y%m%d')='$filter'";
  // h1($q);
       list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
       if(!$opt) return $out; // if no order placed send blank array
       $row = mysqli_fetch_assoc($rs);
          // $id = $row['user_id'];
           $out = $row['totalcall'];
                       
       
       return $out;
   } 
    
    
    
    ################################################################
   public function get_user_attendance($filter1='',  $records = '', $orderby='')
   {
       global $dbc;
       $out = array();		
       // if user has send some filter use them.
       $filterstr = $this->oo_filter($filter1, $records, $orderby);
       $q = "SELECT count(distinct user_daily_attendance.user_id) as dailyattendancecount FROM `user_daily_attendance` 
       left JOIN person ON person.id=user_daily_attendance.user_id 
        $filterstr ";
       // h1($q);
       list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
       if(!$opt) return $out; // if no order placed send blank array
       $row = mysqli_fetch_assoc($rs);
          // $id = $row['user_id'];
           $out = $row['dailyattendancecount'];
                       
       
       return $out;
   } 

 public   function  get_user_count($filter1='',  $records = '', $orderby='')
{
   global $dbc;
   $out = array();
           $filterstr = $this->oo_filter($filter1, $records, $orderby);
   $q = "SELECT count(distinct person.id) as user_count from person 
   inner join person_details on person_details.person_id=person.id 
   inner join person_login on person_login.person_id=person.id   $filterstr   " ;
         //  h1($q);
   list($opt,$rs)= run_query($dbc,$q,$mode='multi',$msg='');
           if(!$opt) return $out;
           $row = mysqli_fetch_assoc($rs);
           
               $id = $row['user_id'];
               $out = $row;

          
           return $out;

}  
###############################
  ################################################################
  public function get_user_order($filter2='',  $records = '', $orderby='')
  {
      global $dbc;
      $out = array();		
      // if user has send some filter use them.
      $filterstr = $this->oo_filter($filter2, $records, $orderby);
      $q = "SELECT count(distinct user_sales_order.user_id) as ordercount FROM `user_sales_order`
       INNER JOIN person ON person.id=user_sales_order.user_id 
       $filterstr  ";
   //   h1($q);
      list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
      if(!$opt) return $out; // if no order placed send blank array
      while($row = mysqli_fetch_assoc($rs))
      {
         // $id = $row['user_id'];
          $out = $row['ordercount'];
                      
      }
      return $out;
  }  
 
########################################
public   function  get_user_dealer_count($filter='',  $records = '', $orderby='')
{
   global $dbc;
   $out = array();
           $filterstr = $this->oo_filter($filter, $records, $orderby);
   $q = "SELECT count(distinct dealer_id) as dealer_count ,count(distinct location_id) as beat_count  FROM dealer_location_rate_list
   inner join person on person.id=dealer_location_rate_list.user_id 
   inner join _role on _role.role_id=person.role_id $filterstr " ;
        //  h1($q);
   list($opt,$rs)= run_query($dbc,$q,$mode='multi',$msg='');
           if(!$opt) return $out;
           while($row = mysqli_fetch_assoc($rs))
           {
               $id = $row['dealer_id'];
               $out = $row;

           }
           return $out;

} 
###########################
public function get_user_beat($id ,$start) {
    global $dbc;
 $out1=array();
 $out=array();
    $q = "SELECT location_5.id as beat_id , location_5.name as beat_name from location_5 inner join user_sales_order as dlrl on 
    dlrl.location_id=location_5.id where dlrl.user_id='$id' and  date_format(dlrl.date,'%Y%m%d')='$start'
     group by dlrl.location_id ";
     //h1($q);
    list($opt, $rs1) = run_query($dbc, $q, $mode = 'multi', $msg = '');
    if ($opt)
    { 
        while($row = mysqli_fetch_array($rs1)){
            
             $out[] = $row['beat_name'];                        
        $out1[] = $row['beat_id']; 
    }
  
    $beat = implode(",", $out);
    $beat_id = implode(",", $out1);
//h1( $beat);
}
        return $beat."|".$beat_id ;
}
######################################
public function get_dealer_deatils($id ,$start){
    //$start_date = date('Y-m-d',strtotime($start_date));
    global $dbc;
    $out1=array();
     $out=array();
    $q="Select dealer.id as did ,
  name AS dealer_name FROM user_sales_order as uso
    INNER JOIN dealer ON dealer.id=uso.dealer_id 
     AND user_id=".$id." and DATE_FORMAT(`date`,'%Y%m%d')='".$start."' group by dealer.id , user_id "; 
    // WHERE DATE_FORMAT(`date`,'%Y-%m-%d')='".$start_date."'
   // h1($q);
    $res1=mysqli_query($dbc,$q);
   while($row=mysqli_fetch_array($res1)) 
    {  
		 $out[] = $row['dealer_name'];                        
        $out1[] = $row['did']; 
 }
     $dealer_name = implode(",", $out);
     $did = implode(",", $out1);
    return $dealer_name."|".$did ;
} 
  #########################################################
  public   function  get_user_dealer_retailer_count($id ,$beat,$did)
  {
     global $dbc;
     if(empty($beat)){
		 $beat='0';
		 }
		 if(empty($did)){
            $did='0';
            }
         // $out = array();
      // $filterstr = $this->oo_filter($filter2, $records, $orderby);
     $q = "SELECT count(distinct retailer_id) as retailer_count FROM user_dealer_retailer inner join 
     retailer on retailer.id=user_dealer_retailer.retailer_id 
     where user_id=".$id." and retailer.location_id in($beat)  and 
     user_dealer_retailer.dealer_id IN($did) "   ;
          //   h1($q);
     list($opt,$rs)= run_query($dbc,$q,$mode='multi',$msg='');
             if(!$opt) return $out;
             while($row = mysqli_fetch_assoc($rs))
             {
                 $id = $row['dealer_id'];
                 $retailer_count = $row['retailer_count'];
  
             }
             return $retailer_count;
  
  } 
  ##########################################
  ###################################### 
  public function get_newretailer($uid ,$filter,  $records = '', $orderby='' )
  {
      global $dbc;
      $out = array();		
      // if user has send some filter use them.
      //$filterstr = $this->oo_filter($uid,$filter1, $records, $orderby);
      //h1($filter);
      $q = "SELECT count(retailer.id) as newretailr FROM `retailer` where  created_by_person_id='$uid'
      and date_format(created_on,'%Y%m%d')='$filter'";
  //h1($q);
      list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
      if(!$opt) return $out; // if no order placed send blank array
      $row = mysqli_fetch_assoc($rs);
         // $id = $row['user_id'];
          $out = $row['newretailr'];
                      
      
      return $out;
  }
  #####################################
  public function get_newoutletsale($uid ,$filter,  $records = '', $orderby='' )
  {
      global $dbc;
      $out = array();		
      // if user has send some filter use them.
      //$filterstr = $this->oo_filter($uid,$filter1, $records, $orderby);
      //h1($filter);
      $q = "Select sum(rate*quantity) AS newretailrsale FROM user_sales_order AS uso INNER JOIN user_sales_order_details USING(order_id) WHERE uso.retailer_id IN(SELECT retailer.id  FROM `retailer` where  created_by_person_id='$uid'
      and date_format(created_on,'%Y%m%d')='$filter') and user_id='$uid'
      and date_format(date,'%Y%m%d')='$filter'";
  //h1($q);
      list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
      if(!$opt) return $out; // if no order placed send blank array
      $row = mysqli_fetch_assoc($rs);
         // $id = $row['user_id'];
          $out = $row['newretailrsale'];
                      
      
      return $out;
  }  
  ##############################################
   #####################################
   public function get_totalsale($uid ,$filter,  $records = '', $orderby='' )
   {
       global $dbc;
       $out = array();		
       // if user has send some filter use them.
       //$filterstr = $this->oo_filter($uid,$filter1, $records, $orderby);
       //h1($filter);
       $q = "Select sum(rate*quantity) AS totalsale FROM user_sales_order AS uso INNER JOIN user_sales_order_details USING(order_id)  where  user_id='$uid'
       and date_format(date,'%Y%m%d')='$filter'";
   //h1($q);
       list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
       if(!$opt) return $out; // if no order placed send blank array
       $row = mysqli_fetch_assoc($rs);
          // $id = $row['user_id'];
           $out = $row['totalsale'];
                       
       
       return $out;
   }  
    #####################################
    public function get_lpsc($uid ,$filter,  $records = '', $orderby='' )
    {
        global $dbc;
        $out = array();		
        // if user has send some filter use them.
        //$filterstr = $this->oo_filter($uid,$filter1, $records, $orderby);
        //h1($filter);
        $q = "Select count(DISTINCT product_id) AS lpsc FROM user_sales_order AS uso INNER JOIN user_sales_order_details USING(order_id)  where  user_id='$uid'
        and date_format(date,'%Y%m%d')='$filter'";
    //h1($q);
        list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
        if(!$opt) return $out; // if no order placed send blank array
        $row = mysqli_fetch_assoc($rs);
           // $id = $row['user_id'];
            $out = $row['lpsc'];
                        
        
        return $out;
    }  
     #####################################
     public function get_tlsd($uid ,$filter,  $records = '', $orderby='' )
     {
         global $dbc;
         $out = array();		
         // if user has send some filter use them.
         //$filterstr = $this->oo_filter($uid,$filter1, $records, $orderby);
         //h1($filter);
         $q = "Select count(product_id) AS lpsc FROM user_sales_order AS uso INNER JOIN user_sales_order_details USING(order_id)  where  user_id='$uid'
         and date_format(date,'%Y%m%d')='$filter'";
     //h1($q);
         list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
         if(!$opt) return $out; // if no order placed send blank array
         $row = mysqli_fetch_assoc($rs);
            // $id = $row['user_id'];
             $out = $row['lpsc'];
                         
         
         return $out;
     }      
}

?>
