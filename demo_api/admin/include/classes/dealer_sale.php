<?php 
class dealer_sale extends myfilter
{
	public $id = NULL;
	public $id_detail = NULL;
	
	public function __construct($id = NULL)
	{
		parent::__construct();
		$this->id = $id;
	}	
	
	//This function is used to fetch the price from hollow section
        public function get_sale_value($rateid,$qty,$rate)
	{
		global $dbc;
		$value = 0;
                switch($rateid)
                {
                    case 0:
                    {
                        $value = $rate * $qty;
                        $value = $value == 0 ? '---' : $value;
                        break;
                    } 
                }
		return $value; 
	}	
       public function get_dealer_sale_se_data()
	{
 		$d1 = $_POST;
		$d1['uid'] = $_SESSION[SESS.'data']['id'];
               
		$d1['myreason'] = 'Please fill all the required information';
		$d1['what'] = 'Dealer Sale Order'; //whether to do history log or not
		return array(true,$d1);	
	}
	
	public function dealer_sale_save()
	{ 
		global $dbc;
		$out= array('status'=>'false','myreason'=>'');
                list($status,$d1)=$this->get_dealer_sale_se_data();
		if(!$status) return array ('staus'=>false,'myreason'=>$d1['myreason']);
                $upload_path = $this->get_document_type_list($filter="id IN (2)",  $records = '', $orderby='');
                
                $sale_path = $upload_path[2]['documents_location'];
                $sale_path = MYUPLOADS.$sale_path;
                $browse_file = $_FILES['image_name']['name'];
                if(!empty( $browse_file))
                {
                    list($uploadstat, $filename) = fileupload('image_name', $sale_path, $allowtype =array('image/jpeg','image/png','image/gif'), $maxsize = 52428800, $mandatory = true);
                    if($uploadstat) 
                    {
                            resizeimage($filename, $sale_path, $newwidth=400, $thumbnailwidth=200, MSYM, $thumbnail = true);			
                    }
                }
                else $filename = '';
                
                $id = $orderno = $d1['uid'].date('YmdHis');
                
                 //Start the transaction
                mysqli_query($dbc, "START TRANSACTION");
                $q = "INSERT INTO `user_sales_order` 
                        (`id`,`order_id`, `user_id`, `retailer_id`,`dealer_id`, `location_id`, `company_id`, `call_status`,`lat_lng`,`mccmnclatcellid`,`date`, `time`,`image_name`,`remarks`) 
                    VALUES ('$id', '$orderno', '$d1[dspId]', '$d1[retailer_id]','$d1[dealer_id]', '$d1[location_id]', '{$_SESSION[SESS.'data']['company_id']}','1',0,0,NOW(), NOW(),'$filename','$d1[remarks]')";
                
                $r = mysqli_query($dbc, $q);
                if(!$r){mysqli_rollback($dbc); return array ('status'=>false, 'myreason'=>'Sales Table error') ;} 
                $rId = mysqli_insert_id($dbc);
                $i=0;
                $total_sale_value = array();
                $total_sale_qty = array();
                if(!empty($d1['product'])) 
                   {
                     foreach($d1['product'] as $key=>$value)
                       {
                         $prod=$d1['product'][$key];
                         $rate=$d1['base_price'][$key];
                         $qty=$d1['quantity'][$key];
                         $schqty=$d1['scheme'][$key];
                         $total1=$d1['prodvalue'][$key];
                         $total_sale_value[] =  $total1;
                         $total_sale_qty[] = $qty;
                         $uncode = $key +1;
                        //To save the value of the other columns as some columns are affected by po
                         $str[] = "('$uncode','$orderno','$prod','$rate','$qty','$schqty')"; 
                       }
                       $str = implode(', ', $str); 
                       $total_sum = array_sum( $total_sale_value);
                       $total_sum_qty = array_sum( $total_sale_qty);
                       
                       $q = "INSERT INTO `user_sales_order_details` (`id`,`order_id`,`product_id`,`rate`,`quantity`,`scheme_qty`) "
                               . "VALUES $str";
                     
                       $r = mysqli_query($dbc, $q);
                       if(!$r) return array ('status'=>false, 'myreason'=>'Sale Order could not be saved') ; 
                   }
                       if(!empty($d1['gift_id']))
                       {
                           $str1 = array();
                           foreach($d1['gift_id'] as $key=>$value)
                           {
                               $gift=$d1['gift_id'][$key];
                               $gift_qty=$d1['gift_qty'][$key];
                               $uncode = $key +1;
                               $str1[] = "('$uncode','$orderno','$gift','$gift_qty')"; 
                           }
                           $str1 = implode(', ', $str1); 
                           $q = "INSERT INTO `user_retailer_gift_details` (`id`,`order_id`,`gift_id`,`quantity`) "
                               . "VALUES $str1";
                           $r = mysqli_query($dbc,$q);
                            if(!$r) return array ('status'=>false, 'myreason'=>'Gift  Table error') ; 
                       }
                       $q11 = "UPDATE user_sales_order SET total_sale_value = '$total_sum', total_sale_qty = '$total_sum_qty' WHERE order_id = '$orderno' ";
                       $r111 = mysqli_query($dbc, $q11);
                       
                       mysqli_commit($dbc);
                
		   //Final success 
                    return array ('status'=>true, 'myreason'=>$d1['what'].' successfully Saved', 'rId'=>$rId);
}
       #################### This function is used to edit sale  order details       
        public function dealer_sale_edit($id)
	{
		global $dbc;
		$out = array('status'=>'false','myreason'=>'');
		list($status,$d1)=$this->get_dealer_sale_se_data();
		if(!$status) return array ('staus'=>false,'myreason'=>$d1['myreason']);
		//Start the transaction
		mysqli_query($dbc, "START TRANSACTION");
		// query to update
		$q="UPDATE user_sales_order SET user_id='$d1[dspId]',retailer_id = '$d1[retailer_id]',call_status = '1',date = NOW(),time = NOW(), remarks = '$d1[remarks]', company_id = '{$_SESSION[SESS.'data']['company_id']}' WHERE order_id = '$id'";
               
		$r=mysqli_query($dbc, $q);
		if(!$r){ mysqli_rollback($dbc); return array ('status'=>false, 'myreason'=>'Retailer Table error') ; }
                $rId = $id;
		$i=0;
                $total_sale_value = array();
                $toal_sale_qty = '';
                $q = "DELETE FROM user_sales_order_details WHERE order_id = '$id'";
                $r = mysqli_query($dbc, $q);
                
                if(!empty($d1['product'])) 
                   {
                     foreach($d1['product'] as $key=>$value)
                       {
                         $prod=$d1['product'][$key];
                         $rate=$d1['base_price'][$key];
                         $qty=$d1['quantity'][$key];
                         $schqty=$d1['scheme'][$key];
                         $total1=$d1['prodvalue'][$key];
                         $total_sale_value[] =  $total1;
                         $toal_sale_qty[] = $qty;
                         $uncode = $key +1;
                        //To save the value of the other columns as some columns are affected by po
                        $str[] = "('$uncode','$id','$prod','$rate','$qty','$schqty')"; 
                       }
                       $str = implode(', ', $str); 
                       $total_sum = array_sum( $total_sale_value);
                       $total_qty_sum = array_sum($toal_sale_qty);
                        $q = "INSERT INTO `user_sales_order_details` (`id`,`order_id`,`product_id`,`rate`,`quantity`,`scheme_qty`) "
                               . "VALUES $str";
                       $r = mysqli_query($dbc, $q);
                       if(!$r) return array ('status'=>false, 'myreason'=>'Sale Order could not be saved') ; 
                   }
                       $q = "DELETE FROM user_retailer_gift_details WHERE order_id = '$id'";
                       if(!empty($d1['gift_id']))
                       {
                           $str1 = array();
                           foreach($d1['gift_id'] as $key=>$value)
                           {
                               $gift=$d1['gift_id'][$key];
                               $gift_qty=$d1['gift_qty'][$key];
                               $uncode = $key+1;
                               $str1[] = "('$uncode','$orderno','$gift','$gift_qty')"; 
                           }
                           $str1 = implode(', ', $str1); 
                           $q = "INSERT INTO `user_retailer_gift_details` (`id`,`order_id`,`gift_id`,`quantity`) "
                               . "VALUES $str1";
                           $r = mysqli_query($dbc,$q);
                           if(!$r) return array ('status'=>false, 'myreason'=>'Gift  Table error') ; 
                       }
                        $q11 = "UPDATE user_sales_order SET total_sale_value = '$total_sum',total_sale_qty = '$total_qty_sum' WHERE order_id = '$id'";
                       
                        $r111 = mysqli_query($dbc, $q11);
                        mysqli_commit($dbc);
		
		return array ('status'=>true,'myreason'=>$d1['what'].' successfully Updated');
	}
	public function get_dealer_sale_list($filter='', $records='', $orderby='')
	{
		global $dbc;
		$out = array();
                $filterstr=$this->oo_filter($filter, $records, $orderby);
                $mtype = $_SESSION[SESS.'constant']['retailer_level'];
		$q = "SELECT *,DATE_FORMAT(date,'%d/%m/%Y') AS sale_date,DATE_FORMAT(time,'%d/%m/%Y') AS sale_time,DATE_FORMAT(date,'%d/%b/%Y') AS fdated FROM user_sales_order $filterstr";
                //h1($q);
                list($opt,$rs)= run_query($dbc,$q,$mode='multi',$msg='');
		if(!$opt) return $out;
                $retailer_map = get_my_reference_array('retailer', 'id', 'name'); 
                $dealer_map = get_my_reference_array('dealer', 'id', 'name');
                $brand_map = get_my_reference_array('catalog_1', 'id', 'name'); 
		while($row = mysqli_fetch_assoc($rs)){
			$id = $row['order_id'];
			$out[$id] = $row; // storing the item id
                        $out[$id]['dspId'] = $row['user_id'];
                        $out[$id]['name'] = $dealer_map[$row['dealer_id']];
                        $out[$id]['firm_name'] = $retailer_map[$row['retailer_id']];
                        $out[$id]['person_name'] = $this->get_username($row['user_id']);
                        $out[$id]['order_item'] = $this->get_my_reference_array_direct("SELECT usod.id,cp.name, cp.taxable,usod.rate,usod.quantity,usod.scheme_qty,usod.product_id FROM user_sales_order_details usod INNER JOIN user_sales_order uso ON usod.order_id = uso.order_id INNER JOIN catalog_product cp ON usod.product_id=cp.id WHERE usod.order_id = $row[order_id]", 'id'); 
                       
                       $out[$id]['gift_item'] = $this->get_my_reference_array_direct("SELECT gift_id,quantity FROM user_retailer_gift_details WHERE order_id = $row[order_id]", 'gift_id'); 
                     
                        
		}// while($row = mysqli_fetch_assoc($rs)){ ends
		return $out;	
	}
        //retailer_add_location
        
        public function calculate_order_item($filter='', $records='', $orderby='')
	{
		global $dbc;
                $qty = 0;
                $sch_qty = 0;
		$out = array('qty'=>0,'scheme_qty'=>0);
                $filterstr=$this->oo_filter($filter, $records, $orderby);
              
                $q = "SELECT (SELECT quantity FROM user_sales_order_details $filterstr) AS quantity,(SELECT scheme_qty FROM user_sales_order_details $filterstr) AS scheme_qty,(SELECT SUM(free_qty)  FROM challan_order_details $filterstr) AS fqty,(SELECT SUM(ch_qty)  FROM challan_order_details $filterstr) AS ch_qty FROM user_sales_order_details LIMIT 1";
               // h1($q);
                list($opt,$rs)= run_query($dbc,$q,$mode='single',$msg='');
		if(!$opt) return $out;
                $qty = $rs['quantity'] - $rs['ch_qty'];
                $sch_qty = $rs['scheme_qty'] - $rs['fqty'];
		return array('qty'=>$qty,'scheme_qty'=>$sch_qty);
	}
        public function get_sale_order_details_list($filter='', $records='', $orderby='')
	{
		global $dbc;
		$out = array();
                $filterstr=$this->oo_filter($filter, $records, $orderby);
		$q = "SELECT *,id FROM user_sales_order_details $filterstr";
                list($opt,$rs)= run_query($dbc,$q,$mode='multi',$msg='');
		if(!$opt) return $out;
               
		while($row = mysqli_fetch_assoc($rs)){
			$id = $row['order_id'];
			$out[$id] = $row; // storing the item id
               
                        
		}// while($row = mysqli_fetch_assoc($rs)){ ends
		return $out;	
	}
        public function dealer_sale_delete($id, $filter='', $records='', $orderby='')
	{
		global $dbc;
                
		if(empty($filter)) {
                    $id = explode('<$>', $id);
                    $order_id = $id[1];
                    $product_id = $id[2];
                    $filter = "order_id = '$order_id' AND product_id = '$product_id'";
                }
               
		$out = array('status'=>false, 'myreason'=>'');
		$deleteRecord = $this->get_sale_order_details_list($filter, $records, $orderby);
		if(empty($deleteRecord)){ $out['myreason'] = 'Product not found'; return $out;}
		//start the transaction
		mysqli_query($dbc, "START TRANSACTION");
		
		//Running the deletion queries
		$delquery = array();
		$delquery['user_sales_order_details'] = "DELETE FROM user_sales_order_details WHERE $filter";
		foreach($delquery as $key=>$value){
			if(!mysqli_query($dbc, $value)){
				mysqli_rollback($dbc);
				return array('status'=>false, 'myreason'=>"$key query failed");
			}
		}
		//After successfull deletion
		mysqli_commit($dbc);
		return array('status'=>true, 'myreason'=>'Product deleted Succesfully');
	}
        //This function used to get user retailer gift deatils
        public function get_dealer_sale_gift_list($filter='', $records='', $orderby='')
	{
		global $dbc;
		$out = array();
                $filterstr=$this->oo_filter($filter, $records, $orderby);
		$q = "SELECT * FROM user_retailer_gift_details  $filterstr";
                list($opt,$rs)= run_query($dbc,$q,$mode='multi',$msg='');
		if(!$opt) return $out;
               
                $gift_map = get_my_reference_array('_retailer_mkt_gift', 'id', 'gift_name'); 
		while($row = mysqli_fetch_assoc($rs)){
			$id = $row['order_id'];
			$out[$id] = $row; // storing the item id
                        $out[$id]['gift_name'] = $gift_map[$row['gift_id']];
                        
                   
		}// while($row = mysqli_fetch_assoc($rs)){ ends
		return $out;	
	}  
        public function get_dsp_wise_user_data($id , $role_id,$dealer_id)
	{
 		global $dbc;
                // here $id is dealer manager id
                $out = array();
                //This query is used to get dealer sale person..
                $q = "SELECT id FROM person WHERE person_id_senior = '$id'";
                list($opt,$rs) = run_query($dbc, $q, 'multi');
                if($opt)
                {
                    while($row = mysqli_fetch_assoc($rs))
                    {
                        $out[$row['id']] = $row['id'];
                    }
                }
                $q = "SELECT user_id FROM user_dealer_retailer WHERE dealer_id = '$dealer_id'";
                list($opt,$rs) = run_query($dbc, $q, 'multi');
                if($opt)
                {
                    while($row = mysqli_fetch_assoc($rs))
                    {
                        $out[$row['user_id']] = $row['user_id'];
                    }
                }
                //pre($out);
                return $out;
	}
        public function get_retailer_challan_no($filter='', $records='', $orderby='')
        {
            global $dbc;
            $out = array();
            $filterstr=$this->oo_filter($filter, $records, $orderby);
            $q = "SELECT id, ch_no FROM challan_order $filterstr";
            //h1($q);
            list($opt, $rs) = run_query($dbc, $q, 'multi');
            if(!$opt) return $out;
            while($row = mysqli_fetch_assoc($rs))
            {
                $id = $row['id'];
                $out[$id] = $row;
            }
            return $out;
        }
        public function get_challan_checkbox_list($filter = '', $records='', $orderBy='')
        {
            global $dbc;
            $out = array();
            $filterstr=$this->oo_filter($filter, $records, $orderby);
            $q = "SELECT challan_no FROM challan_order_wise_payment_details $filterstr";
            list($opt, $rs) = run_query($dbc, $q, 'multi');
            if(!$opt) return $out;
            while($row = mysqli_fetch_assoc($rs))
            {
                $id = $row['challan_no'];
                $out[$id] = $id;
            }
            return $out;
        }
        public function get_total_challan_value($filter = '', $records='', $orderBy='')
        {
            global $dbc;
            $out = NULL;
            $filterstr=$this->oo_filter($filter, $records, $orderby);
            $q = "SELECT SUM(product_rate * ch_qty) AS rvalue FROM challan_order_details $filterstr";
            list($opt, $rs) = run_query($dbc, $q, 'single');
            if(!$opt) return $out;
            return $rs['rvalue'];
        }
        //This function is used to find out in which firm  person belongs
         public function get_dealer_user_sale_data($id , $role_id)
	{
 		global $dbc;
                $out = array();
                // here $id is dealer manager id
                $dealer_id = $this->get_dealer_id($id, $role_id);
                $q = "SELECT user_id FROM user_dealer_retailer INNER JOIN person ON person.id = user_dealer_retailer.user_id INNER JOIN _role USING(role_id) WHERE dealer_id = '$dealer_id' AND role_group_id = '11'";
                list($opt, $rs) = run_query($dbc, $q, 'multi');
                if($opt){
                    while($rows = mysqli_fetch_assoc($rs))
                    {
                        $out[$rows['user_id']] =  $rows['user_id'];
                    }
                }
                return $out;
	}
        public function get_dealer_location_list($id)
	{
		global $dbc;
		$out = array();
                $filterstr=$this->oo_filter($filter, $records, $orderby);
                $q = "SELECT location_".$_SESSION[SESS.'retailer_level'].".id,location_".$_SESSION[SESS.'retailer_level'].".name FROM dealer_location_rate_list "
                                . "INNER JOIN location_".$_SESSION[SESS.'dealer_level']." ON location_".$_SESSION[SESS.'dealer_level'].".id=dealer_location_rate_list.location_id";
                for($i = $_SESSION[SESS.'dealer_level'];$i<$_SESSION[SESS.'retailer_level'] ;$i++)
                {
                    $j = $i + 1; 
                    $q .= " INNER JOIN location_$j ON location_$j.location_".$i."_id = location_$i.id ";
                }
                $q .= " WHERE dealer_location_rate_list.dealer_id=".$id;
               //h1($q);
                list($opt, $rs) = run_query($dbc, $q, 'multi');
                if(!$opt) return $out;
                while($row = mysqli_fetch_assoc($rs)){
                  $out[$row['id']] = $row['name'];  
                }
		return $out;	
	}  
        public function get_user_wise_sale_data($id , $role_id)
	{
 		global $dbc;
		$out = array();
                $main_id = $id;
                if($role_id == 1) {
                $q = "SELECT user_id FROM user_sales_order ORDER BY id DESC";
		list($opt,$rs)= run_query($dbc,$q,$mode='multi',$msg='');
                if(!$opt) return $out;
                while($row = mysqli_fetch_assoc($rs)){
                    $id = $row['user_id'];
                    $out[$id] = $id; // storing the item id
                 }// while($row = mysqli_fetch_assoc($rs)){ ends
              } // if($role_id == 1) end here
              else {
                   $q = "SELECT role_id FROM _role WHERE role_id = '$role_id' OR senior_role_id >= '$role_id' AND role_group_id = '11'";
                   list($opt,$rs)= run_query($dbc,$q,$mode='multi',$msg='');
                   if(!$opt) return $out;
                   $role_id_array = array();
                   //$role_id_array[$role_id] = $role_id;
                   while($row = mysqli_fetch_assoc($rs)){
                       $role_id_array[$row['role_id']] = $row['role_id'];  
                   }
                  $out[$main_id] = $main_id;
                  $role_id_str = implode(',',$role_id_array);
                  $q = "SELECT person.id FROM person INNER JOIN user_sales_order ON user_sales_order.user_id = person.id WHERE role_id IN ($role_id_str) AND person_id_senior = '$main_id'";
                  list($opt,$rs)= run_query($dbc,$q,$mode='multi',$msg='');
                  if(!$opt) return $out;
                   while($row = mysqli_fetch_assoc($rs)){
                       $out[$row['id']] = $row['id'];  
                   }
              }
              
            return $out;
	}
        public function get_dealer_id($id, $role_id)
	{
		global $dbc;
		$out = NULL;
		$q = "SELECT dealer_id FROM user_dealer_retailer INNER JOIN person ON person.id = user_dealer_retailer.user_id WHERE user_id = '$id' AND role_id = '$role_id' LIMIT 1";
               // h1($q);
		list($opt,$rs)= run_query($dbc,$q,$mode='single',$msg='');
                if(!$opt) return $out;
                else return $rs['dealer_id'];
		
	}
        public function next_order_num()
	{
		global $dbc;
		$out = array();
		$q = "SELECT MAX(id) AS total FROM user_sales_order";
		list($opt,$rs)= run_query($dbc,$q,$mode='single',$msg='');
		//return $rs['total']+1;	
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
       
        public function sale_delete($id, $filter='', $records='', $orderby='')
	{
		global $dbc;
		if(empty($filter)) $filter = "itemId = $id";
		$out = array('status'=>false, 'myreason'=>'');
		$deleteRecord = $this->get_sale_list($filter="order_id=$id", $records, $orderby);
		if(empty($deleteRecord)){ $out['myreason'] = 'user sales order not found'; return $out;}
		//start the transaction
		mysqli_query($dbc, "START TRANSACTION");
		
		//Running the deletion queries
		$delquery = array();
		$delquery['user_sales_order'] = "DELETE FROM user_sales_order WHERE order_id = $id LIMIT 1";
		$delquery['user_sales_order_details'] = "DELETE FROM user_sales_order_details WHERE order_id = $id";
		foreach($delquery as $key=>$value){
			if(!mysqli_query($dbc, $value)){
				mysqli_rollback($dbc);
				return array('status'=>false, 'myreason'=>'$key query failed');
			}
		}
		//After successfull deletion
		mysqli_commit($dbc);
		return array('status'=>true, 'myreason'=>'Sales Order successfully deleted');
	}
     
        // This function is used to set automatic path for all the document
        public function get_document_type_list($filter='',  $records = '', $orderby='')
	 {
		global $dbc;
		$out = array();		
		//if user has send some filter use them.
		$filterstr = $this->oo_filter($filter, $records, $orderby);
                $q = "SELECT * FROM _document_type $filterstr";
		list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
		if(!$opt) return $out; // if no order placed send blank array
		while($row = mysqli_fetch_assoc($rs))
		{
			$id = $row['id'];
			$out[$id] = $row; // storing the item id    
		}
		return $out;
	}  
        ############# DSP WISE CHALLAN WORKING START HERE ############################
        public function get_challan_se_data()
	{
 		$d1 = $_POST;
		$d1['uid'] = $_SESSION[SESS.'data']['id'];
		$d1['myreason'] = 'Please fill all the required information';
		$d1['what'] = 'Challan'; //whether to do history log or not
		return array(true,$d1);	
	}
	
	public function challan_save()
	{ 
		global $dbc;
		$out= array('status'=>'false','myreason'=>'');
		list($status,$d1)= $this ->get_challan_se_data();  
		if(!$status) return array('status'=>false, 'myreason'=>$d1['myreason']);
		//Manipulation and value reading
		$chnum = $this->next_challan_num();
		$dispatch = empty($d1['dispatch_date']) ? '' : get_mysql_date($d1['dispatch_date'], '/', false, false); 
                $ch_date = empty($d1['ch_date']) ? '' : get_mysql_date($d1['ch_date'], '/', false, false); 
		//Start the transaction
               //$_SESSION['chalan_id'] = $chnum;
               //$_SESSION['chalan_dealer_id'] = $d1[dealer_id];
             // $uid=date(Ymdhis);
              //h1($uid);
		mysqli_query($dbc, "START TRANSACTION");		
		$q = "INSERT INTO `challan_order` (`id`, `ch_no`, `ch_created_by`, `ch_dealer_id`, `ch_retailer_id`, `dispatch_date`, `ch_date`, `company_id`) 
			VALUES (NULL, '$chnum','$d1[uid]', '$d1[dealer_id]', '$d1[retailer_id]', '$dispatch', '$ch_date', '{$_SESSION[SESS.'data']['company_id']}');";
		$r = mysqli_query($dbc, $q);
		if(!$r){mysqli_rollback($dbc); return array ('status'=>false, 'myreason'=>'Challan Could Not saved, Some error occurred') ;} 
		$rId = mysqli_insert_id($dbc);	
		$extrawork = $this->challan_extra('save', $chnum, $_POST['order_id']); 
		if(!$extrawork['status']){ mysqli_rollback($dbc); return array ('status'=>false, 'myreason'=>$extrawork['myreason']);} 
                
                if(!empty($d1['product_id']))
                {
                 $str = array();
                 $product_details_id = array();
                 foreach($d1['product_id'] as $key=>$value)
                       {
                         $prod = $d1['product_id'][$key];
                         $rate=$d1['base_price'][$key];
                         $batch_no = $d1['batch_no'][$key];
                         $catalog_details_id = $d1['product_details'][$key];
                         $product_details_id[] = $d1['product_details'][$key];
                         $taxId = $d1['taxId'][$key];
                         $qty=$d1['quantity'][$key];
                         $schqty=$d1['scheme'][$key];
                        //To save the value of the other columns as some columns are affected by po
                        $str[] = "(NULL,'$chnum','$prod', '$catalog_details_id', '$qty', '$rate','$schqty', '$d1[uid]', '$taxId', '$batch_no')"; 
                       }
                       $str = implode(',' ,$str);
                       $q = "INSERT INTO challan_order_details (`id`,`challan_no`,`product_id`, `catalog_details_id`, `ch_qty`,`product_rate`, `free_qty`, `order_id`, `taxId`, `batch_no`) VALUES $str";
                    
                       $r = mysqli_query($dbc , $q);
                       if(!$r) { mysqli_rollback($dbc); return array ('status'=>false, 'myreason'=>'DSP items can not be added succesfully.');} 
                       $this->calculate_stock($d1[uid], $product_details_id);
                       mysqli_commit($dbc);
                       return array ('status'=>true, 'myreason'=>$d1['what'].' successfully Saved', 'rId'=>$rId);
                }
	}
	
        ##################### This function is used to save data in challan order details ############
	public function challan_extra($actiontype, $chnum, $order_id)
	{ 
		global $dbc;
		$qty_sum = array();
		//during update we are required to remove the previous entry
		if($actiontype == 'update') mysqli_query($dbc, "DELETE FROM challan_order_details WHERE $chnum = $chnum");	
                
		foreach($order_id as $key=>$value){
                        $total_dispatch_qty = array();
                        $str = '';
                        $product = $_POST["product_id$value"];
                        $product_details = array();
                        if(is_array($product)) {
                            foreach($product as $inkey=>$invalue){
                                $qty = $_POST["ch_qty$value"][$inkey]; // challan qty
                                $total_dispatch_qty[] = $qty;
                                $prate = $_POST["product_rate$value"][$inkey];
                                $fqty = $_POST["free_qty$value"][$inkey];
                                $product_details = $_POST["product_details$value"][$inkey];
                                $catalog_details_id[] = $_POST["product_details$value"][$inkey];
                                $batch_no = $_POST["batch_nos$value"][$inkey];
                                $taxId = $_POST["taxId$value"][$inkey];
                                $ch_sale_value = $_POST["ch_sale_value$value"][$inkey];
                                $str.= '(NULL,\''.$chnum.'\',\''.$invalue.'\', \''.$product_details.'\', \''.$qty.'\', \''.$prate.'\', \''.$fqty.'\',\''.$value.'\', \''.$taxId.'\', \''.$batch_no.'\'),';
                                //$str.= '(NULL,\''.$chnum.'\',\''.$invalue.'\', \''.$qty.'\', \''.$prate.'\', \''.$fqty.'\',\''.$value.'\', \''.$taxId.'\', \''.$batch_no.'\'),';
                                
                            } //foreach($product as $inkey=>$invalue){ end here
                            $str = rtrim($str, ',');
                            $toal_dispatch_sum = array_sum($total_dispatch_qty);
                            $q = "INSERT INTO challan_order_details (`id`,`challan_no`,`product_id`, `catalog_details_id`, `ch_qty`,`product_rate`, `free_qty`, `order_id`, `taxId`, `batch_no`) VALUES $str";
                            $r = mysqli_query($dbc, $q);
                            if(!$r) return array ('status'=>false, 'myreason'=>'Challan Order Details Could not be saved Some error occurred.') ;
                            $this->calculate_stock($value, $catalog_details_id);
                            $this->update_order_status($value);
                            //return array ('status'=>true, 'myreason'=>'') ;	
                        } //if(is_array($product)) { end here	
		} //foreach($order_id as $key=>$value){ end here
                //$check_status = $this->get_dispatch_status();
		return array ('status'=>true, 'myreason'=>'') ;	
	}
    public function calculate_stock($order_id , $product_details_id)
    {
        global $dbc;
        if(!empty($product_details_id))
        $product_details_id_str = implode(',' ,$product_details_id);
        $q = "SELECT ch_qty, catalog_details_id FROM challan_order_details WHERE order_id = '$order_id' AND  catalog_details_id IN ($product_details_id_str)";
        list($opt, $rs) = run_query($dbc, $q, 'multi');
        if($opt)
        {
            while($row = mysqli_fetch_assoc($rs))
            {
                $q = "UPDATE catalog_product_details SET ostock = ostock - '$row[ch_qty]' WHERE id = '$row[catalog_details_id]'";
                $r = mysqli_query($dbc,$q);
                
            }
        }
    }
    public function update_order_status($id)
    {
        global $dbc;
        $out = TRUE;
        $q = "SELECT product_id, quantity FROM user_sales_order_details WHERE order_id = '$id'";
        list($opt,$rs) = run_query($dbc, $q, 'multi');
        if(!$opt) return $out;
        $num = mysqli_num_rows($rs);
        $inc = 0;
        while($row = mysqli_fetch_assoc($rs))
        {
             $q = "SELECT SUM(ch_qty) AS ch_qty FROM challan_order_details WHERE product_id = '$row[product_id]' AND order_id = '$id'";
            
            list($opt1,$rs1) = run_query($dbc,$q,'single');
            if($opt1)
            {
                if($rs1['ch_qty'] >= $row['quantity']){
                    $inc++;
                }
            }
        }            
//        h1($num);
//        h1($inc); exit;
        if($num == $inc ) {
            $q = "UPDATE user_sales_order SET order_status = '1' WHERE order_id = '$id'";
            $r = mysqli_query($dbc, $q);
            if($r) return $out;
        }
    }
   public function challan_delete($id, $filter='', $records='', $orderby='')
	{
		global $dbc;
		if(empty($filter)) $filter = "ch_no = $id";
		$out = array('status'=>false, 'myreason'=>'');
		$deleteRecord = $this->get_dsp_challan_list($filter, $records, $orderby);
		if(empty($deleteRecord)){ $out['myreason'] = 'Challan not found'; return $out;}
		//start the transaction
		mysqli_query($dbc, "START TRANSACTION");
		//Checking whether the invoice is deletable or not
                $q = "SELECT batch_no, ch_qty FROM challan_order_details WHERE challan_no = '$id'";
		list($opt, $rs) = run_query($dbc, $q, 'multi');
                if($opt) 
                {
                    while($row = mysqli_fetch_assoc($rs))
                    {
                        $q = "UPDATE catalog_product_details SET ostock = ostock + '$row[ch_qty]' WHERE id = '$row[batch_no]'";
                        $r = mysqli_query($dbc,$q);
                    }
                    
                }
                $q = "SELECT `order_id` FROM `challan_order_details` WHERE challan_no = '$id'";
                $r = mysqli_query($dbc, $q);
                
                if($r && mysqli_num_rows($r) > 0)
                {
                    while($row = mysqli_fetch_assoc($r))
                    {
                         $q2 = "UPDATE user_sales_order SET `order_status` = '2' WHERE order_id = '$row[order_id]'";
                         $r2 = mysqli_query($dbc, $q2);
                    }
                }
            
		//Running the deletion queries
		$delquery = array();
		$delquery['challan_order'] = "DELETE FROM challan_order WHERE ch_no = $id LIMIT 1";
		$delquery['challan_order_details'] = "DELETE FROM challan_order_details WHERE challan_no = $id";
		foreach($delquery as $key=>$value){
			if(!mysqli_query($dbc, $value)){
				mysqli_rollback($dbc);
				return array('status'=>false, 'myreason'=>"$key query failed");
			}
		}
		//After successfull deletion
               
		mysqli_commit($dbc);
		return array('status'=>true, 'myreason'=>'Challan successfully deleted');
	}
    public function get_dsp_challan_list($filter='', $records='', $orderby='')
    {
            global $dbc;
            $out = array();
            $filterstr=$this->oo_filter($filter, $records, $orderby);
            $q = "SELECT *, DATE_FORMAT(dispatch_date, '".MASKDATE."') AS dispatch_date, DATE_FORMAT(ch_date, '".MASKDATE."') AS ch_date FROM challan_order $filterstr";
            // h1($q);
            list($opt,$rs)= run_query($dbc,$q,$mode='multi',$msg='');
            if(!$opt) return $out; 
            $dealer_map = get_my_reference_array('dealer', 'id', 'name'); 
            $retailer_map = get_my_reference_array('retailer', 'id', 'name'); 
            while($row = mysqli_fetch_assoc($rs)){
                    $id = $row['ch_no'];
                    $out[$id] = $row; // storing the item id
                    $out[$id]['dealer_name'] = $dealer_map[$row['ch_dealer_id']];
                    $out[$id]['retailer_name'] = $retailer_map[$row['ch_retailer_id']];
                    $out[$id]['challan_order_details'] = $this->get_my_reference_array_direct("SELECT challan_order_details.*, catalog_product.name, _tax.name AS taxname, _tax.value AS tvalue FROM challan_order_details INNER JOIN catalog_product ON catalog_product.id = challan_order_details.product_id LEFT JOIN _tax ON _tax.id = challan_order_details.taxId  WHERE challan_no = $id ", 'id'); 
                  
            }// while($row = mysqli_fetch_assoc($rs)){ ends
            return $out;	
    }
        ######################################## Invoice ends here ####################################################
	public function print_looper_challan($multiId, $options=array())
	{
		global $dbc;
		$out = array();
		if(is_null($multiId) || empty($multiId)) return $out;
		
		//Create the object when needed
		$party = new retailer();		
		//Explode to get an array of all the Id
		$multiId = explode('-', $multiId);
		foreach($multiId as $key=>$value){
			$id = trim($value);
			if(empty($id)) continue;
			
			//read the record statistics
			$rcdstat = $this->get_dsp_challan_list("challan_order.id = $id");
			if(empty($rcdstat)) continue;
			$temp = $rcdstat[$id];

      // pre($temp);
			
			$out[$id] = $rcdstat[$id];
      $out[$id]['adr'] = $party->get_retailer_adr($temp['ch_retailer_id']);

      $did = $temp['ch_dealer_id'];
      $q = "SELECT *, l3.id, l3.name,l4.name as city FROM dealer_location_rate_list INNER JOIN location_$dealer_level AS l3 ON l3.id = dealer_location_rate_list.location_id INNER JOIN location_4 AS l4 ON l3.location_4_id = l4.id WHERE dealer_id = '$did'";
			$out[$id]['dealer_addr'] = $this->get_my_reference_array_direct($q, 'id');
		}
		//pre($out);
		return $out;
	}
	
        public function next_challan_num()
	{
		global $dbc;
		$out = array();
		$q = "SELECT MAX(ch_no) AS total FROM challan_order";
		list($opt,$rs)= run_query($dbc,$q,$mode='single',$msg='');
		return $rs['total']+1;	
	}
        
        ############# DSP WISE CHALLAN WORKING END HERE ############################
        
        public function direct_challan_save()
	{        
		global $dbc;
                
		$out= array('status'=>'false','myreason'=>'');
		list($status,$d1)= $this ->get_challan_se_data();  
		if(!$status) return array('status'=>false, 'myreason'=>$d1['myreason']);
                $orderno= date('YmdHis');
		//Manipulation and value reading
		$chnum = $this->next_challan_num();
                h1($chnum);
                h1($d1[dealer_id]);
		$dispatch = empty($d1['dispatch_date']) ? '' : get_mysql_date($d1['dispatch_date'], '/', false, false); 
                $ch_date = empty($d1['ch_date']) ? '' : get_mysql_date($d1['ch_date'], '/', false, false); 
		//Start the transaction
                $_SESSION['chalan_id'] = $chnum;
                $_SESSION['chalan_dealer_id'] = $d1[dealer_id];
		mysqli_query($dbc, "START TRANSACTION");		
                
               echo $q = "INSERT INTO `challan_order` (`id`, `ch_no`, `ch_created_by`, `ch_dealer_id`, `ch_retailer_id`, `dispatch_date`, `ch_date`, `company_id`) 
			VALUES ($orderno, '$chnum','$d1[uid]', '$d1[dealer_id]', '$d1[retailer_id]', '$dispatch', '$ch_date', '{$_SESSION[SESS.'data']['company_id']}');";
		
                $r = mysqli_query($dbc, $q);
		if(!$r){mysqli_rollback($dbc); return array ('status'=>false, 'myreason'=>'Challan Could Not saved, Some error occurred') ;} 
		$rId = mysqli_insert_id($dbc);	
		
                if(!empty($d1['product']))
                {
                 $str = array();
                 $str1 = array();
                 $batch_id = array();
                //pre($d1); exit;
                 $total_sale_value = 0;
                 $total_qty = 0;
                 foreach($d1['product1'] as $key=>$value)
                       {
                         $prod=$d1['product'][$key];
                         $rate=$d1['base_price'][$key];
                         $batch_no = $d1['product1'][$key];
                         $batch_id[] = $d1['product1'][$key];
                         $taxId = $d1['taxId'][$key];
                         $qty=$d1['quantity'][$key];
                         $schqty=$d1['scheme'][$key];
                         $total_sale_value = $total_sale_value + $d1['base_price'][$key] * $d1['quantity'][$key];
                         $total_qty = $total_qty + $d1['quantity'][$key];
                        //To save the value of the other columns as some columns are affected by po
                        $str[] = "(NULL,'$chnum','$prod','$qty', '$rate','$schqty', '$d1[uid]', '$taxId', '$batch_no')"; 
                        $str1[] = "(NULL,'$orderno','$prod','$rate', '$qty','$schqty')";
                       }
                     
                       $str = implode(',' ,$str);
                       $q = "INSERT INTO challan_order_details (`id`,`challan_no`,`product_id`,`ch_qty`,`product_rate`, `free_qty`, `order_id`, `taxId`, `batch_no`) VALUES $str";
                      
                       $r = mysqli_query($dbc , $q);
                       if(!$r) { mysqli_rollback($dbc); return array ('status'=>false, 'myreason'=>'DSP items can not be added succesfully.');} 
                       $this->calculate_stock($d1[uid], $batch_id);
                       $str1 = implode(',' ,$str1);
                       $q = "INSERT INTO `user_sales_order_details` (`id`,`order_id`,`product_id`,`rate`,`quantity`,`scheme_qty`) "
                               . "VALUES $str1";                     
                       $r = mysqli_query($dbc, $q);
                       if(!$r) return array ('status'=>false, 'myreason'=>'Sale Order could not be saved') ; 
                }
              $q = "INSERT INTO `user_sales_order`  (`order_id`, `user_id`, `retailer_id`,`dealer_id`, `location_id`, `call_status`,`lat_lng`,`mccmnclatcellid`,`date`, `time`,`total_sale_value`,`total_sale_qty`, `order_status`, `company_id`) VALUES ('$orderno', '$d1[uid]', '$d1[retailer_id]','$d1[dealer_id]', '$d1[location_id]', 'True',0,0,NOW(), NOW(),'$total_sale_value','$total_qty', '1', '{$_SESSION[SESS.'data']['company_id']}')";
                
                $r = mysqli_query($dbc, $q);
                if(!$r){mysqli_rollback($dbc); return array ('status'=>false, 'myreason'=>'Sales Table error') ;} 
		mysqli_commit($dbc);
		//Final success 
		return array ('status'=>true, 'myreason'=>$d1['what'].' successfully Saved', 'rId'=>$rId);
	}
        public function get_dealer_location_id_list($id)
	{
		global $dbc;
		$out = array();
                $filterstr=$this->oo_filter($filter, $records, $orderby);
                $q = "SELECT location_".$_SESSION[SESS.'retailer_level'].".id,location_".$_SESSION[SESS.'retailer_level'].".name FROM dealer_location_rate_list "
                . "INNER JOIN location_".$_SESSION[SESS.'dealer_level']." ON location_".$_SESSION[SESS.'dealer_level'].".id=dealer_location_rate_list.location_id";
                for($i = $_SESSION[SESS.'dealer_level'];$i<$_SESSION[SESS.'retailer_level'] ;$i++)
                {
                    $j = $i + 1; 
                    $q .= " INNER JOIN location_$j ON location_$j.location_".$i."_id = location_$i.id ";
                }
                $q .= " WHERE dealer_location_rate_list.dealer_id=".$id;
               // h1($q);
                list($opt, $rs) = run_query($dbc, $q, 'multi');
                if(!$opt) return $out;
                while($row = mysqli_fetch_assoc($rs)){
                  $out[$row['id']] = $row['id'];  
                }
		return $out;	
	}
      
}
?>