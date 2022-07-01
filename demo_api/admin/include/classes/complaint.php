<?php 
class complaint extends myfilter
{
	public $id = NULL;
	public $id_detail = NULL;
	
	public function __construct($id = NULL)
	{
		parent::__construct();
		$this->id = $id;
	}	
	######################################## Invoice Starts here ####################################################
	
       public function get_complaint_se_data()
	{
 		$d1 = $_POST;
		$d1['uid'] = $_SESSION[SESS.'data']['id'];
                $d1['company_id'] = $_SESSION[SESS.'data']['company_id'];
		$d1['myreason'] = 'Please fill all the required information';
		$d1['what'] = 'Complaint'; //whether to do history log or not
		return array(true, $d1);	
	}
	
	public function complaint_save()
	{ 
		global $dbc;
		$out= array('status'=>'false','myreason'=>'');
                list($status,$d1)=$this->get_complaint_se_data();
		if(!$status) return array ('staus'=>false,'myreason'=>$d1['myreason']);
                $orderno = date('YmdHis');
                $receive_date = !empty($d1['receive_date']) ? get_mysql_date($d1['receive_date']) : '';
                $id = $d1['uid'].date('Ymdhis');
                //Start the transaction
             //   pre($d1);
                mysqli_query($dbc, "START TRANSACTION");
               $q = "INSERT INTO `complaint_history`(`complaint_id`, `msg`, `date`) VALUES('$d1[complaint_id]','$d1[complaint_msg]',NOW())";
                $r = mysqli_query($dbc, $q);
                if(!$r){mysqli_rollback($dbc); return array ('status'=>false, 'myreason'=>'complaint_history Table error') ;} 
              $q1 = "UPDATE `complaint` SET`action`= '$d1[complaint_type]' WHERE `complaint_id`='$d1[complaint_id]'";
              $r1 = mysqli_query($dbc, $q1);
              
                if(!$r1){mysqli_rollback($dbc); return array ('status'=>false, 'myreason'=>'complaint Table error') ;} 
              
                mysqli_commit($dbc);
                //Final success 
                return array ('status'=>true, 'myreason'=>$d1['what'].' successfully Saved', 'rId'=>$rId);
         } 
       
        public function complaint_edit($id)
	{
		global $dbc;
		$out = array('status'=>'false','myreason'=>'');
		list($status,$d1)=$this->get_retailer_se_data();
		if(!$status) return array ('staus'=>false,'myreason'=>$d1['myreason']);
                $orderno = $this->next_order_num();
                $total_sale_value = $this->get_sale_value($d1['catalog_1_id'],$d1['metric_ton']);         
		//Start the transaction
		mysqli_query($dbc, "START TRANSACTION");
		// query to update
		$q="UPDATE user_sales_order SET user_id='$d1[uid]',retailer_id = '$d1[retailer_id]',order_id='$orderno',call_status = '$d1[call_status]',total_sale_value = '$d1[total_sale_value]',sale_date = NOW(),sale_time = NOW(), company_id = '$d1[company_id]' WHERE id = '$id'";
		
                $r=mysqli_query($dbc, $q);
		if(!$r){mysqli_rollback($dbc); return array ('status'=>false, 'myreason'=>'Retailer Table error') ;}
                $rId = $id;
		mysqli_commit($dbc);
		return array ('status'=>true,'myreason'=>$d1['what'].' successfully Updated');
	}
	public function get_complaint_list($filter='', $records='', $orderby='')
	{
		global $dbc;
		$out = array();
	      $compliant= 'Complaint_report';
	      
                $filterstr=$this->oo_filter($filter, $records, $orderby);
                $mtype = $_SESSION[SESS.'constant']['retailer_level'];
		$q = "SELECT complaint_type.name as complaint_typename , $compliant.*,location_view.l3_id as state_id,location_view.l3_name as state_name FROM `$compliant`
		 INNER JOIN complaint_type ON complaint_type.id=$compliant.complaintID
		 INNER JOIN person ON person.id=$compliant.user_id 
		 INNER JOIN location_view ON location_view.l3_id=person.state_id $filterstr  ";
       // h1($q);
                list($opt,$rs)= run_query($dbc,$q,$mode='multi',$msg='');
		if(!$opt) return $out;
              
		while($row = mysqli_fetch_assoc($rs)){
			$id = $row['id'];
			$out[$id] = $row; 
                        $out[$id]['user_name'] = $this->get_username($row['user_id']);
                        $out[$id]['type'] = $this->get_type($row['complaint_type']);
                        $cid = $row['complaint_id'];
                        $out[$id]['msg'] = $this->get_my_reference_array_direct("SELECT *, date as cdate FROM `complaint_history` WHERE complaint_history.complaint_id = $cid", 'id');  
                   
		}// while($row = mysqli_fetch_assoc($rs)){ ends
           //  pre($out);
		return $out;	
	}
        public function complaint_list($filter='', $records='', $orderby='')
	{
		global $dbc;
		$out = array();
                h1($filter);
                $filterstr=$this->oo_filter($filter, $records, $orderby);
                $mtype = $_SESSION[SESS.'constant']['retailer_level'];
		$q = "SELECT * FROM `complaint` where $filter ";
               // h1($q);
                list($opt,$rs)= run_query($dbc,$q,$mode='multi',$msg='');
		if(!$opt) return $out;
            
		while($row = mysqli_fetch_assoc($rs)){
			$id = $row['order_id'];
			$out[$id] = $row; 
                        $out[$id]['user_name'] = $this->get_username($row['user_id']);
                        $out[$id]['type'] = $this->get_type($row['complaint_type']);
                        $cid = $row['complaint_id'];
                        $out[$id]['msg'] = $this->get_my_reference_array_direct("SELECT *, date as cdate FROM `complaint_history` 
			WHERE complaint_history.complaint_id = $cid", 'id');  
                   
		}// while($row = mysqli_fetch_assoc($rs)){ ends
           //  pre($out);
		return $out;	
	}
        //This function used to get user retailer gift deatils
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
        
       
       
}
?>