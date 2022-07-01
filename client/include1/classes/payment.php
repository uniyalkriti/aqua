<?php
// This class will handle all the task related to packing slip in and bill of packing slips
class payment extends myfilter
{
	public $poid = NULL;
	public function __construct()
	{
		parent::__construct();
	}
	
	######################################## Payment code Starts here ####################################################	
	public function get_challan_payment_se_data()
	{  
		$d1 = $_POST;
		$d1['myreason'] = 'Please fill all the required information';
		$d1['what'] = 'Payment'; //whether to do history log or not
		return array(true,$d1);
	}
        public function get_challan_sub_payment_se_data()
	{  
		$d1 = $_POST;
		$d1['myreason'] = 'Please fill all the required information';
		$d1['what'] = 'Payment Collection'; //whether to do history log or not
		return array(true,$d1);
	}
	
	public function payment_save()
	{
		global $dbc;	
		$out = array('status'=>false, 'myreason'=>'');
		list($status, $d1) = $this->get_challan_payment_se_data();
		if(!$status) return array('status'=>false, 'myreason'=>$d1['myreason']);		if(!empty($d1['payment_date'])) 
                    $payment = get_mysql_date($d1['payment_date']);
                 else $payment = '';
                $pay_num = $this->next_payment_num();
                $pay_num = "DS{$_SESSION[SESS.'data']['dealer_id']}/{$_SESSION[SESS.'sess']['short_period']}/$pay_num";
                $id = $_SESSION[SESS.'data']['dealer_id'].date('Ymdhis');
		//start the transaction
               // pre($d1);
		mysqli_query($dbc, "START TRANSACTION");
		// query to save
		  $q = "INSERT INTO `challan_order_wise_payment` (`pay_id`, `retailer_id`, `payment_date`, `pay_amount`) VALUES ('$id', '$d1[retailer_id]', '$payment', '$d1[pay_amount]')";
		$r = mysqli_query($dbc,$q) ;
		if(!$r){mysqli_rollback($dbc); return array('status'=>false, 'myreason'=>'department table error');}
		$rId = $id;	
               // h1($rId);
                $extrawork = $this->payment_extra('save', $rId, $d1['challan_no']); 
		if(!$extrawork['status']){ mysqli_rollback($dbc); return array ('status'=>false, 'myreason'=>$extrawork['myreason']);} 
                
                $payment_status = $this->payment_status($_POST['challan_no']); 
		mysqli_commit($dbc);
                //Logging the history		
		//Final success
		return array('status'=>true, 'myreason'=>$d1['what'].' successfully Saved', 'rId'=>$rId);
	}
	public function payment_extra($actiontype, $payId, $challan_no)
	{ 
            //h1($payId);
		global $dbc;
		$str = array();
		//during update we are required to remove the previous entry
		if($actiontype == 'Update') mysqli_query($dbc, "DELETE FROM challan_order_wise_payment_details WHERE pay_id = '$payId'");	
                if(!empty($challan_no))
                {
                    if(is_array($challan_no)){
                    foreach ($challan_no as $key=>$value)
                    {
                       $str[] = '(NULL, \''.$payId.'\', \''.$value.'\')'; 
                    }
                    $str = implode($str, ',');
                    }else{
                      $str = '(NULL, \''.$payId.'\', \''.$challan_no.'\')'; 
                    }
                }
                
                $q = "INSERT INTO challan_order_wise_payment_details (`ch_pay_id`, `pay_id`, `challan_no`) VALUES $str";
               
                $r = mysqli_query($dbc, $q);
                if(!$r) return array ('status'=>false, 'myreason'=>'payment Details Could not be saved Some error occurred.') ;
		return array ('status'=>true, 'myreason'=>'') ;	
	}
	public function payment_edit($id)
	{
		global $dbc;	
		$out = array('status'=>false, 'myreason'=>'');
		list($status, $d1) = $this->get_challan_payment_se_data();
		if(!$status) return array('status'=>false, 'myreason'=>'Please fill all the required information');
                $payment = !empty($d1['payment_date']) ? get_mysql_date($d1['payment_date']) : '';
		//Checking whether the original data was modified or not
		//start the transaction
		mysqli_query($dbc, "START TRANSACTION");
		// query to update 		
		$q = "UPDATE `challan_order_wise_payment` SET `retailer_id` = '$d1[retailer_id]', `payment_date` = '$payment', pay_amount = '$d1[pay_amount]'  WHERE pay_id = '$id'";
		$r = mysqli_query($dbc,$q);
		if(!$r){mysqli_rollback($dbc); return array('status'=>false, 'myreason'=>'department table error');}
                $rId = $id;
                $extrawork = $this->payment_extra('Update', $rId, $_POST['challan_no']); 
		if(!$extrawork['status']){ mysqli_rollback($dbc); return array ('status'=>false, 'myreason'=>$extrawork['myreason']);} 
                $payment_status = $this->payment_status($_POST['challan_no']);
		mysqli_commit($dbc);
		
		//Saving the user modification history
		return array('status'=>true, 'myreason'=>$d1['what'].' successfully updated', 'rId'=>$rId);
	}	
	
	public function get_payment_list($filter='',  $records = '', $orderby='')
	{
		global $dbc;
		$out = array();		
		// if user has send some filter use them.
		$filterstr = $this->oo_filter($filter, $records, $orderby);
               // echo $filterstr;
	//$q = "SELECT *,DATE_FORMAT(payment_date, '%e/%b/%Y') AS payment_date FROM challan_order_wise_payment $filterstr ";
        $q = "SELECT *,DATE_FORMAT(pay_date, '%e/%b/%Y') AS payment_date,(select name from retailer where id=payment_enrollment.retailer_id) as retailer_name  FROM payment_enrollment $filterstr ";
    // h1($q);
		list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
		if(!$opt) return $out; // if no order placed send blank array
                $retailer_map = get_my_reference_array('retailer', 'id', 'name'); 
              //  print_r($retailer_map);
		while($row = mysqli_fetch_assoc($rs))
		{
                    //$id = $row['pay_id'];
                    $id = $row['id'];
                    $out[$id] = $row;                             
                    $out[$id]['name'] = $retailer_map[$row['retailer_id']];
                    $q1 = "SELECT * FROM challan_order_wise_payment_details  WHERE pay_id = $id ";         
                    $out[$id]['challan_details'] = $this->get_my_reference_array_direct($q1, 'ch_pay_id'); 
                   
		}
              //  pre($out);exit;
		return $out;
	} 
        public function payment_sub_save()
	{
		global $dbc;	
		$out = array('status'=>false, 'myreason'=>'');
		list($status, $d1) = $this->get_challan_sub_payment_se_data();
		if(!$status) return array('status'=>false, 'myreason'=>$d1['myreason']);		
                if(!empty($d1['chq_date'])) 
                        $chq_date = get_mysql_date($d1['chq_date']);
                 else $chq_date = '';
                //$pay_num = $this->next_payment_num();
               // $pay_num = "DS{$_SESSION[SESS.'data']['dealer_id']}/{$_SESSION[SESS.'sess']['short_period']}/$pay_num";
                $id = $_SESSION[SESS.'data']['dealer_id'].date('Ymdhis');
                $dealer_id = $_SESSION[SESS.'data']['dealer_id'];
		//start the transaction
               // pre($d1);
		mysqli_query($dbc, "START TRANSACTION");
		// query to save
            
		  $q = " INSERT INTO payment_enrollment (`id`,`dealer_id`,`location_id`,`retailer_id`,`user_id`,`pay_mode`,`amount`,`bank_name`,`cheque_number`,`pay_date`,`cheque_date`)VALUES('$id','$dealer_id','$d1[location_id]','$d1[retailer_id]','$d1[user_id]','$d1[pay_mode]','$d1[amount]','$d1[bank_name]','$d1[chq_no]',CURDATE(),'$chq_date]')";
               
		$r = mysqli_query($dbc,$q) ;
		if(!$r){mysqli_rollback($dbc); return array('status'=>false, 'myreason'=>'Payment table error');}
		$rId = $id;	
               // h1($rId);
//                $extrawork = $this->payment_extra('save', $rId, $d1['challan_no']); 
//		if(!$extrawork['status']){ mysqli_rollback($dbc); return array ('status'=>false, 'myreason'=>$extrawork['myreason']);} 
//                
//                $payment_status = $this->payment_status($_POST['challan_no']); 
		mysqli_commit($dbc);
                //Logging the history		
		//Final success
		return array('status'=>true, 'myreason'=>$d1['what'].' successfully Saved', 'rId'=>$rId);
	}

	function save_payment(){
		global $dbc;
                
		$out = array('status'=>false, 'myreason'=>'');
		list($status, $d1) = $this->get_challan_sub_payment_se_data();

		 if(empty($d1['chq_date'])) 
                        $chq_date = $d1['txn_date'];
                 else $chq_date = $d1['chq_date'];
                 
                 if(empty($d1['chq_no'])) 
                        $chq_no = $d1['txn_no'];
                 else $chq_no = $d1['chq_no'];
               $id = $_SESSION[SESS.'data']['dealer_id'].date('Ymdhis');
                $dealer_id = $_SESSION[SESS.'data']['dealer_id'];
                
               $challan_id = implode(',',$d1['ch_id']);
		//start the transaction 
              //echo $challan_id;
                    //   pre($d1);exit;
		mysqli_query($dbc, "START TRANSACTION");
		// query to save
            
		  //$q = " INSERT INTO payment_enrollment (`id`,`dealer_id`,`location_id`,`retailer_id`,`user_id`,`pay_mode`,`amount`,`bank_name`,`cheque_number`,`pay_date`,`cheque_date`)VALUES('$id','$dealer_id','$d1[location_id]','$d1[retailer_id]','$d1[user_id]','$d1[pay_mode]','$d1[amount]','$d1[bank_name]','$d1[chq_no]',CURDATE(),'$chq_date]')";

		  $q="INSERT INTO `payment_collection`(`id`, `dealer_id`, `retailer_id`, `challan_id`, `total_amount`, `pay_mode`, `bank_name`, `chq_no`, `chq_date`, `Remark`, `pay_date_time`) VALUES ('','$dealer_id','$d1[retailer_id]','$challan_id','$d1[remain_amt]','$d1[pay_mode]','$d1[bank_name]','$chq_no','$chq_date','$d1[Remark]',NOW())";
               //h1($q); exit;
		$r = mysqli_query($dbc,$q) ;
		if(!$r){mysqli_rollback($dbc); return array('status'=>false, 'myreason'=>'Payment table error');}
		$rId = $id;	

		//mysqli_query($dbc,"UPDATE `challan_order` SET `payment_status`='1' where `id` IN($challan_id)");
                $amt=$d1[remain_amt];
                $challan_id1=explode(',',$challan_id);
                $size=  sizeof($challan_id1);
                 for($i=0;$i<$size;$i++){
                    $qwer="SELECT  `challan_order`.`remaining`
                    FROM  `challan_order` 
                    WHERE  `challan_order`.`id`='$challan_id1[$i]' order by auto ASC";
                    //h1($qwer);exit;
                    $rs1=mysqli_query($dbc, $qwer);
                      while ($row1 = mysqli_fetch_assoc($rs1)){
                       
                      $remain_amt=$row1['remaining'];
                    //  $remain_amt=number_format((float)$remain_amt1, 2, '.', '');
                      
                       if($amt>=$remain_amt)
                           {
                       $amt=$amt-$remain_amt;
                       $q23="UPDATE `challan_order` SET  `remaining` =  '0',`payment_status`='1' WHERE `challan_order`.`id` =$challan_id1[$i]";
                      // h1($q23);exit;
                      mysqli_query($dbc,$q23);   
                       }
                        else if($amt < $remain_amt && $amt > 0){
                               $remain=$remain_amt-$amt; 
                               $amt=0;
                                mysqli_query($dbc,"UPDATE `challan_order` SET  `remaining` =  '$remain',`payment_status`='2' WHERE `challan_order`.`id` =$challan_id1[$i]");   
                            }
                       }
                 //mysqli_query($dbc,"UPDATE `challan_order_details` SET  `remain_amount` =  '' WHERE `challan_order_details`.`id` =$challan_id1[$i]");
                 }
         
		mysqli_commit($dbc);
                //Logging the history		
		//Final success
		return array('status'=>true, 'myreason'=>$d1['what'].' successfully Saved', 'rId'=>$rId);


	}
        
        public function get_payment_search_list($filter='',  $records = '', $orderby='')
	{
		global $dbc;
		$out = array();		
		// if user has send some filter use them.
		$filterstr = $this->oo_filter($filter, $records, $orderby);
	$q = "SELECT DATE_FORMAT(ch_date,'%d/%m/%Y')as date,sum(qty * product_rate) as total,sum(((qty * product_rate)*tax)/100)as tax,ch_no, ch_retailer_id,co.id as uid from challan_order co INNER JOIN challan_order_details cod ON co.id = cod.ch_id INNER JOIN retailer ON retailer.id = co.ch_retailer_id $filterstr ";
      // h1($q);
		list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
		if(!$opt) return $out; // if no order placed send blank array
                $retailer_map = get_my_reference_array('retailer', 'id', 'name'); 
		while($row = mysqli_fetch_assoc($rs))
		{
                    $id = $row['uid'];
                    $out[$id] = $row;
                    $out[$id]['name'] = $retailer_map[$row['ch_retailer_id']];
                   
		}
               // pre($out);
		return $out;
	} 
        public function next_payment_num()
        {
            global $dbc;
            $out = NULL;
            $q = "SELECT COUNT(pay_id) AS total FROM challan_order_wise_payment ORDER BY pay_id ASC";
            list($opt,$rs)= run_query($dbc,$q,$mode='single',$msg='');
            if($opt) $out = $rs['total']+1;
            return $out;
            
        }
	public function payment_status($challan_no='')
        {
            global $dbc;
            $challan_no_str = '';
            if(!empty($challan_no)){
                if(is_array($challan_no)){
            $challan_no_str = implode(',' , $challan_no); 
                }  else {
                    $challan_no_str = $challan_no;
                }
            }
            $q = "SELECT (SELECT SUM(pay_amount) AS pamt FROM challan_order_wise_payment INNER JOIN challan_order_wise_payment_details USING(pay_id) WHERE challan_no IN ($challan_no_str)) AS total_payment_value,(SELECT SUM(product_rate * ch_qty) AS total_challan_value FROM challan_order INNER JOIN challan_order_details ON challan_order_details.challan_no = challan_order.ch_no WHERE challan_order_details.challan_no IN ($challan_no_str)) AS total_challan_value FROM challan_order_wise_payment LIMIT 1";
            list($opt, $rs) = run_query($dbc, $q, 'single');
            if($opt)
            {
                if($rs['total_payment_value'] >= $rs['total_challan_value'])
                {
                    $q = "UPDATE challan_order SET dispatch_status = '2' WHERE ch_no IN ($challan_no_str)";
                    $r = mysqli_query($dbc, $q);
                }
            }
            
        }
	######################################## Plate Planner Ends here ######################################################
	public function payment_delete($id, $filter='', $records='', $orderby='')
	{
		global $dbc;
		if(empty($filter)) $filter = "itemId = $id";
		$out = array('status'=>false, 'myreason'=>'');
		$deleteRecord = $this->get_item_list($filter, $records, $orderby);
		if(empty($deleteRecord)){ $out['myreason'] = 'Item not found'; return $out;}
		//start the transaction
		mysqli_query($dbc, "START TRANSACTION");
		//Checking whether the invoice is deletable or not
		$q['PR'] = "SELECT itemId FROM pr_item WHERE itemId = ";
		$q['PO'] = "SELECT itemId FROM pur_order_item WHERE itemId = ";
		$found = false;
		foreach($q as $key=>$value)
		{
			$q1 = "$value $id LIMIT 1";
			list($opt1, $rs1) = run_query($dbc, $q1, $mode='single', $msg='');	
			if($opt1) {$found = true; $found_case = $key; break; }
		}
		// If this item has been found in any one of the above query we can not delete it.				
		if($found) {$out['myreason'] = 'Item  entered in <b>'.$found_case.'</b> so could not be deleted.'; return $out;}
		
		//Running the deletion queries
		$delquery = array();
		$delquery['stock'] = "DELETE FROM item WHERE itemId = $id LIMIT 1";
		$delquery['stock_item'] = "DELETE FROM stock_item WHERE itemId = $id";
		foreach($delquery as $key=>$value){
			if(!mysqli_query($dbc, $value)){
				mysqli_rollback($dbc);
				return array('status'=>false, 'myreason'=>'$key query failed');
			}
		}
		//After successfull deletion
		mysqli_commit($dbc);
		return array('status'=>true, 'myreason'=>'Item successfully deleted');
	}
	public function print_looper_payment($multiId, $options=array())
	{
		global $dbc;
		$out = array();
		if(is_null($multiId) || empty($multiId)) return $out;
		
		$item = new item();
		//Explode to get an array of all the Id
		$multiId = explode('-', $multiId);
		foreach($multiId as $key=>$value){
			$id = trim($value);
			if(empty($id)) continue;			
			//read the record statistics
			$rcdstat = $this->get_nesting_list("nestingId = $id");
			if(empty($rcdstat)) continue;
			$temp = $rcdstat[$id];
			//$out = $temp;
			$out[$id] = $temp;
			//$out[$id]['sf_item'] = $this->get_my_reference_array_direct("SELECT * FROM job_route_batch_item INNER JOIN item USING(itemId) WHERE jrbId = $temp[jrbId]", 'itemId');
			//$out[$id]['adr'] = $party->get_party_adr($temp['partyId']);*/
		}
		//pre($out);
		return $out;
	}
        
        
        
        public function get_payment_wise_retailer_list($location_id)
        {
            global $dbc;
            $out = array();
            $rlevel = $_SESSION[SESS.'constant']['retailer_level'];
            $dlevel = $_SESSION[SESS.'constant']['dealer_level'];
            $q = "SELECT retailer.id FROM retailer INNER JOIN location_$rlevel ON location_$rlevel.id = retailer.location_id ";
            for($i = $rlevel; $i >= $dlevel; $i--)
            {
                $j = $i - 1;
                $q .= " INNER JOIN location_$j ON location_$j.id = location_$i.location_".$j."_id";
            }
            $q .= " WHERE location_$dlevel.id = '$location_id'";
            h1($q);
            list($opt, $rs) = run_query($dbc, $q, 'multi');
            if(!$opt) return $out;
            while($row = mysqli_fetch_assoc($rs))
            {
                $out[$row['id']] = $row['id'];
            }
            return $out;
        }
        
        
     /*     public function get_payment_retailer($dealer_id){
            
            global $dbc;
            $out = array();
           $q="SELECT id AS retailer_id, name AS retailer_name FROM `retailer` WHERE `dealer_id` = '$dealer_id'";
           list($opt,$rs)= run_query($dbc,$q,$mode='multi',$msg='');
           if(!$opt) return $out; 
                while($row = mysqli_fetch_assoc($rs))
		{                  
                    //$out[$id] = $row;   
                    
                    $id = $row['retailer_id'];
                    $out[$id] = $row;
                    $out[$id]['retailer_id'] = $row['retailer_id'];
                    $out[$id]['retailer_name'] = $row['retailer_name'];
                    
                }
           // print_r($out); exit;
           return $out;                
        }
        
        public function get_payment_location($dealer_id){
            
            global $dbc;
            $out = array();
           $q="SELECT location_id as beat_id,(select name from location_5 where id=dealer_location_rate_list.location_id) as beat_name FROM `dealer_location_rate_list` WHERE dealer_id='$dealer_id'";
           list($opt,$rs)= run_query($dbc,$q,$mode='multi',$msg='');
           if(!$opt) return $out; 
                while($row = mysqli_fetch_assoc($rs))
		{                  
                    //$out[$id] = $row;   
                    
                    $id = $row['beat_id'];
                    $out[$id] = $row;
                    $out[$id]['beat_id'] = $row['beat_id'];
                    $out[$id]['beat_name'] = $row['beat_name'];
                    
                }
           // print_r($out); exit;
           return $out;                
        }*/
}// class end here
?>