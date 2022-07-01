<?php 
class primary_sale extends myfilter
{
	public $id = NULL;
	public $id_detail = NULL;
	
	public function __construct($id = NULL)
	{
		parent::__construct();
		$this->id = $id;
	}	
	######################################## Invoice Starts here ####################################################
	
       public function get_primary_sale_order_se_data()
	{
 		$d1 = $_POST;
		$d1['uid'] = $_SESSION[SESS.'data']['id'];
                $d1['company_id'] = $_SESSION[SESS.'data']['company_id'];
		$d1['myreason'] = 'Please fill all the required information';
		$d1['what'] = 'Primary Sale Order'; //whether to do history log or not
		return array(true, $d1);	
	}
	
	public function primary_sale_order_save()
	{ 
		global $dbc;
		$out= array('status'=>'false','myreason'=>'');
                list($status,$d1)=$this->get_primary_sale_order_se_data();
		if(!$status) return array ('staus'=>false,'myreason'=>$d1['myreason']);
                $orderno = date('YmdHis');
                $receive_date = !empty($d1['receive_date']) ? get_mysql_date($d1['receive_date']) : '';
                $id = $d1['uid'].date('Ymdhis');
                //Start the transaction
                mysqli_query($dbc, "START TRANSACTION");
               $q = "INSERT INTO `user_primary_sales_order` 
                    (`id`, `order_id`,  `dealer_id`,`created_date`, `created_person_id`, `sale_date`,`receive_date`,`date_time`, `company_id`) 
                VALUES ('$id', '$orderno' ,'$d1[dealer_id]', NOW(), '$d1[uid]',NOW(), '$receive_date' ,NOW(), '$d1[company_id]')"; 
                
                $r = mysqli_query($dbc, $q);
if(!$r){mysqli_rollback($dbc); return array ('status'=>false, 'myreason'=>'Sales Table error') ;} 
                
                $rId = $id;
                $extrawork = $this->primary_sales_order_extra('save', $orderno, $_POST['product_id'], $_POST['batch_no'],$_POST['base_price'],$_POST['quantity'],$_POST['scheme'], $_POST['mfg_date'], $_POST['expiry_date']);                
                
                if(!$extrawork['status']){mysqli_rollback($dbc); return array ('status'=>false, 'myreason'=>$extrawork['myreason']);} 
                
                mysqli_commit($dbc);
                //Final success 
                return array ('status'=>true, 'myreason'=>$d1['what'].' successfully Saved', 'rId'=>$rId);
         } 
        public function primary_sales_order_extra($actiontype, $rId, $productId, $batch_no, $base_price,$quantity, $scheme,$mfg_date,$expiry_date)
	{ 
		global $dbc;		
		$uncode = '';
		$str = $str_cat  = array();
                $company_id = $_SESSION[SESS.'data']['company_id'];
		//during update we are required to remove the previous entry
		if($actiontype == 'update') mysqli_query($dbc, "DELETE FROM user_primary_sales_order_details WHERE order_id = $rId");
		// saving the details for the stock item table
		foreach($productId as $key=>$value){
			$mfdate = !empty($mfg_date[$key]) ? get_mysql_date($mfg_date[$key]) : '';
                        $expdate =  !empty($expiry_date[$key]) ? get_mysql_date($expiry_date[$key]) : '';
                        $uncode = date('Ymdhis') + $key + 1;
			$str[] = "('$uncode', '$rId', '$value', '{$base_price[$key]}', '{$quantity[$key]}', '{$scheme[$key]}', '$mfdate', '$expdate', '{$batch_no[$key]}')";
                        $str_cat[] = "('$uncode','$value', '{$batch_no[$key]}', '{$quantity[$key]}', '{$base_price[$key]}', '$mfdate', '$expdate', NOW())";
		}
		$str = implode(', ', $str);
                $str_cat = implode(', ', $str_cat);
		 $q = "INSERT INTO `user_primary_sales_order_details` (`id`, `order_id`, `product_id`, `rate`, `quantity`, `scheme_qty`, `mfg_date`, `expiry_date`, `batch_no`) VALUES $str";
               
		$r = mysqli_query($dbc, $q);
		if(!$r) return array ('status'=>false, 'myreason'=>'User Primary Sales could not be saved some error occurred.') ;
                $q = "INSERT INTO `catalog_product_details` (`id`, `product_id`, `batch_no`, `ostock`, `rate`, `mfg_date`, `expiry_date`, `created`) VALUES $str_cat";
              
		$r = mysqli_query($dbc, $q);
		if(!$r) return array ('status'=>false, 'myreason'=>'User Primary Sales could not be saved, some error occurred in catalog_product_details.') ;
		return array ('status'=>true,'myreason'=>'');
	}       
        public function primary_sale_order_edit($id)
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
		//Saving the user modification history
		//$hid = history_log($dbc, 'Edit', 'stock received <strong>'.$d1['billno'].'</strong> With RefCode :'.$id);
		//$this->save_log($hid, $modifieddata, $d1['what']);
		return array ('status'=>true,'myreason'=>$d1['what'].' successfully Updated');
	}
	public function get_primary_sale_order_list($filter='', $records='', $orderby='')
	{
		global $dbc;
		$out = array();
                $filterstr=$this->oo_filter($filter, $records, $orderby);
                $mtype = $_SESSION[SESS.'constant']['retailer_level'];
		$q = "SELECT *,DATE_FORMAT(sale_date,'%d/%m/%Y') AS sale_date,DATE_FORMAT(receive_date,'%d/%m/%Y') AS receive_date,DATE_FORMAT(date_time,'%d/%b/%Y') AS fdated FROM user_primary_sales_order LEFT JOIN dealer ON dealer.id=user_primary_sales_order.dealer_id $filterstr";
               // h1($q);
                list($opt,$rs)= run_query($dbc,$q,$mode='multi',$msg='');
		if(!$opt) return $out;
                $dealer_map = get_my_reference_array('dealer', 'id', 'name');
                $csa_map = get_my_reference_array('csa', 'c_id', 'csa_name');
                $brand_map = get_my_reference_array('catalog_1', 'id', 'name'); 
		while($row = mysqli_fetch_assoc($rs)){
			$id = $row['order_id'];
			$out[$id] = $row; // storing the item id
                        $out[$id]['name'] = $dealer_map[$row['dealer_id']];
                        $out[$id]['cfa'] = $csa_map[$row['csa_id']];
                        $out[$id]['person_name'] = $this->get_username($row['created_person_id']);
                        $out[$id]['order_item'] = $this->get_my_reference_array_direct("SELECT usod.id,cp.name,usod.rate,usod.quantity,usod.scheme_qty, batch_no,usod.cases,usod.pcs,usod.pr_rate  FROM user_primary_sales_order_details usod INNER JOIN user_primary_sales_order uso ON usod.order_id = uso.order_id INNER JOIN catalog_product cp ON usod.product_id=cp.id INNER JOIN catalog_2 c2 ON c2.id = cp.catalog_id WHERE usod.order_id = $row[order_id]", 'id');  
                    
		}// while($row = mysqli_fetch_assoc($rs)){ ends
              
		return $out;	
	}
        //This function used to get user retailer gift deatils
        public function get_username($id)
	{
		global $dbc;
		$out = NULL;
		$q = "SELECT CONCAT_WS(' ',first_name,middle_name,last_name) AS uname FROM person WHERE id = $id";
		list($opt,$rs)= run_query($dbc,$q,$mode='single',$msg='');
                if(!$opt) return $out;
		return $rs['uname'];	
	}
        public function next_primary_order_num()
	{
		global $dbc;
		$out = array();
		$q = "SELECT MAX(id) AS total FROM user_sales_order";
		list($opt,$rs)= run_query($dbc,$q,$mode='single',$msg='');
		//return $rs['total']+1;	
	}
       
        public function primary_sale_delete($id, $filter='', $records='', $orderby='')
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
}
?>