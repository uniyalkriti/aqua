<?php
class report extends myfilter
{
	
	function __construct()
	{
		parent::__construct();
	}

    public function get_user_leave_list($filter = '', $records = '', $orderby = '') {
        global $dbc;

        $out = array();

        // if user has send some filter use them.
        $filterstr = $this->oo_filter($filter, $records, $orderby);
        $q = "SELECT _role.rolename,uda.user_id,uda.attn_address,uda.remarks,lt.name as lt_name,ltc.name as ltc_name,lto.name as lto_name ,uda.id AS uid, 
        DATE_FORMAT(uda.leave_date,'%e/%b/%Y') AS wdate,uda.server_date_time as server_date, 
        DATE_FORMAT(uda.leave_date,'%Y%m%d') AS wdatess,
         DATE_FORMAT(uda.from_date,'%e/%b/%Y') AS fromdate,
          DATE_FORMAT(uda.to_date,'%e/%b/%Y') AS todate, 
        DATE_FORMAT(uda.leave_date,'%H:%i:%s') AS wtime, CONCAT_WS(' ',first_name,middle_name,last_name) AS name,from_date,to_date,leave_category_id,uda.status  
        FROM user_leave_request uda 
                INNER JOIN person ON person.id = uda.user_id 
                INNER JOIN person_login ON person.id=person_login.person_id AND person_login.person_status='1' 
                INNER JOIN _leave_type lt ON uda.leave_id = lt.id 
                INNER JOIN _leave_type_category ltc ON uda.leave_category_id = ltc.id
                INNER JOIN _leave_type_option lto ON uda.leave_category_option_id = lto.id  
                INNER JOIN _role ON person.role_id = _role.role_id $filterstr ";

        //$q = "call proc_attendance_report()";
        // h1($show);
        $rs = mysqli_query($dbc, $q);
        //if(!$opt) return $out; // if no order placed send blank array
        while ($row = mysqli_fetch_assoc($rs)) {
            $id = $row['uid'];
            $out[$id] = $row;
            //pre($row);
        }
        // pre($out);
        return $out;
    }

	//This function will return the detail about a particular vendor
	public function get_row_material_stcok_list($filter='',  $records = '', $orderby='')
	{
		global $dbc;
		$out = array();		
		// if user has send some filter use them. invoice_date
		$filterstr = $this->oo_filter($filter, $records, $orderby);
		$q = "SELECT * FROM item INNER JOIN  item_group USING(groupId) $filterstr ";
		list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
		if(!$opt) return $out;
		while($row = mysqli_fetch_assoc($rs)){
			$id = $row['itemId'];
			$out[$id] = $row;
			$out[$id]['stock_item'] = get_central_stock($id);
		}
		return $out;
	}
	
	public function get_itemwise_report($filter='',  $records = '', $orderby='')
	{
		global $dbc;
		$out = array();		
		// if user has send some filter use them. invoice_date GROUP BY ItemId
		$filterstr = $this->oo_filter($filter, $records, $orderby);
		$q = "SELECT DATE_FORMAT(invdate, '".MASKDATE."') AS invdatef, partyId, itemId, qty, rate, invnum, invoiceId FROM invoice INNER JOIN invoice_item USING(invoiceId)  $filterstr";
		list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
		$vendorId_map = get_my_reference_array('party', 'partyId', 'partyname');
		$itemId = get_my_reference_array('item', 'itemId', 'itemname'); 
		if(!$opt) return $out;
		$inc = 1;
		while($row = mysqli_fetch_assoc($rs)){
			$id = $row['partyId'];
			//$out[$id] = $row;
			$out[$id]['partyId'] = $row['partyId'];
			$out[$id]['partyId_val'] = $vendorId_map[$row['partyId']];
			$out[$id]['itemstat'][$row['itemId']]['itemname'] = $itemId[$row['itemId']];
			$out[$id]['itemstat'][$row['itemId']]['billstat'][$inc]['invnum'] = $row['invnum'];
			$out[$id]['itemstat'][$row['itemId']]['billstat'][$inc]['invdatef'] = $row['invdatef'];
			$out[$id]['itemstat'][$row['itemId']]['billstat'][$inc]['qty'] = $row['qty'];
			$out[$id]['itemstat'][$row['itemId']]['billstat'][$inc]['basicval'] = $row['qty']*$row['rate'];
			$inc++;
			
			//echo "SELECT * , DATE_FORMAT(created, '".MASKDATE."') AS created FROM invoice INNER JOIN invoice_item USING(invoiceId) WHERE itemId = '$row[itemId]' ORDER BY created ASC";
			//$out[$id]['invoice_item'] = $this->get_my_reference_array_direct($q="SELECT * , DATE_FORMAT(invdate, '".MASKDATE."') AS invdatef FROM invoice INNER JOIN invoice_item USING(invoiceId) WHERE itemId = '$row[itemId]' AND partyId = '$row[partyId]'  ORDER BY invdate ASC", 'invoiceId');
		}
		return $out;
	}
            
	public function get_itemwise_report1($filter='',  $records = '', $orderby='')
	{
		global $dbc;
		$out = array();		
		// if user has send some filter use them. invoice_date GROUP BY ItemId
		$filterstr = $this->oo_filter($filter, $records, $orderby);
		echo $q = "SELECT partyId, itemId, invoiceId, DATE_FORMAT(invoice.created, '".MASKDATE."') AS created, DATE_FORMAT(invdate, '".MASKDATE."') AS invdate FROM invoice INNER JOIN invoice_item USING(invoiceId)  $filterstr GROUP BY partyId";
		list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
		$vendorId_map = get_my_reference_array('party', 'partyId', 'partyname');
		$itemId = get_my_reference_array('item', 'itemId', 'itemname'); 
		if(!$opt) return $out;
		while($row = mysqli_fetch_assoc($rs)){
			$id = $row['invoiceId'];
			$out[$id] = $row;
			$out[$id]['partyId_val'] = $vendorId_map[$row['partyId']];
			$out[$id]['itemname'] = $itemId[$row['itemId']];
			//echo "SELECT * , DATE_FORMAT(created, '".MASKDATE."') AS created FROM invoice INNER JOIN invoice_item USING(invoiceId) WHERE itemId = '$row[itemId]' ORDER BY created ASC";
			$out[$id]['invoice_item'] = $this->get_my_reference_array_direct($q="SELECT * , DATE_FORMAT(invdate, '".MASKDATE."') AS invdatef FROM invoice INNER JOIN invoice_item USING(invoiceId) WHERE itemId = '$row[itemId]' AND partyId = '$row[partyId]'  ORDER BY invdate ASC", 'invoiceId');
			echo '<br>'.$q; 
		}
		return $out;
	}
	
	public function get_powise_report($filter='',  $records = '', $orderby='')
	{
		global $dbc;
		$out = array();		
		// if user has send some filter use them. invoice_date GROUP BY ItemId
		$filterstr = $this->oo_filter($filter, $records, $orderby);
		$q = "SELECT itemId, sum(qty) AS qtysum, sum(qty*rate) AS ratesum, itemname  FROM invoice INNER JOIN invoice_item USING(invoiceId) INNER JOIN item USING(itemId) $filterstr GROUP BY itemId";
		list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
		//$vendorId_map = get_my_reference_array('party', 'partyId', 'partyname');
		//$itemId = get_my_reference_array('item', 'itemId', 'itemname'); 
		if(!$opt) return $out;
		while($row = mysqli_fetch_assoc($rs)){
			$id = $row['itemId'];
			$out[$id] = $row;
			//$out[$id]['partyId_val'] = $vendorId_map[$row['partyId']];
		}
		return $out;
	}
	
	public function get_tarrif_headwise_report($filter='',  $records = '', $orderby='')
	{
		global $dbc;
		$out = array();		
		// if user has send some filter use them. invoice_date GROUP BY ItemId
		$filterstr = $this->oo_filter($filter, $records, $orderby);
		$q = "SELECT tarrifhead, itemId, qty, rate, invoiceId FROM invoice INNER JOIN invoice_item USING(invoiceId)  $filterstr";
		list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
		$vendorId_map = get_my_reference_array('party', 'partyId', 'partyname');
		$itemId = get_my_reference_array('item', 'itemId', 'itemname'); 
		if(!$opt) return $out;
		$inc = 1;
		while($row = mysqli_fetch_assoc($rs)){
			$id = $row['tarrifhead'];
			//$out[$id] = $row;
			$out[$id]['tarrifhead'] = $row['tarrifhead'];
			$out[$id]['num'] = $inc;
			$out[$id]['itemstat'][$row['itemId']]['itemname'] = $itemId[$row['itemId']];
			$out[$id]['itemstat'][$row['itemId']]['qty'][] = $row['qty'];
			$out[$id]['itemstat'][$row['itemId']]['basicval'][] = $row['qty']*$row['rate'];
			$inc++;
			
			//echo "SELECT * , DATE_FORMAT(created, '".MASKDATE."') AS created FROM invoice INNER JOIN invoice_item USING(invoiceId) WHERE itemId = '$row[itemId]' ORDER BY created ASC";
			//$out[$id]['invoice_item'] = $this->get_my_reference_array_direct($q="SELECT * , DATE_FORMAT(invdate, '".MASKDATE."') AS invdatef FROM invoice INNER JOIN invoice_item USING(invoiceId) WHERE itemId = '$row[itemId]' AND partyId = '$row[partyId]'  ORDER BY invdate ASC", 'invoiceId');
		}
		return $out;
	}
        // This function is used to calculate physical stock of items.
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


        public function get_distributor_stock_list($filter='',  $records = '', $orderby='',$filter_str)
	{
		global $dbc;
                $out = array();
		$filterstr = $this->oo_filter($filter_str, $records, $orderby);
             //pre($filter);
       
     $q = "Select dealer.id as id,dealer.name as dname,l2_name as branch FROM dealer INNER JOIN dealer_location_rate_list dlrl ON dlrl.dealer_id=dealer.id INNER JOIN user_dealer_retailer udr ON udr.dealer_id=dlrl.dealer_id INNER JOIN location_view lv ON lv.l5_id=dlrl.location_id  $filterstr GROUP BY dealer.id";
                
             //h1($q);
		list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
		$retailer_map = get_my_reference_array('retailer', 'id', 'name');
		$dealer_map = get_my_reference_array('dealer', 'id', 'name'); 
	        if($opt)
                {
                    while($row = mysqli_fetch_assoc($rs)){
                            $id = $row['id'];
                            $out[$id] = $row;
                            $out[$id]['saleable'] = $this->get_stock($filter[0],$id);    
                    }
                }
              return $out;
	}
        

         public function get_stock($date,$id)
	{
		global $dbc;
                $newdate=date('Ymd');
                if(empty($date)){
                $date="DATE_FORMAT(sale_date,'%Y%m%d')="."$newdate";
                //h1($date);
                }
		$out = NULL;
	        $q = "SELECT COUNT(DISTINCT(product_id)) as saleable FROM user_primary_sales_order_details upsod INNER JOIN user_primary_sales_order upso ON upso.order_id=upsod.order_id INNER JOIN dealer ON dealer.id=upso.dealer_id WHERE $date AND upso.dealer_id='$id'";
        //    h1($q);
                list($opt,$rs)= run_query($dbc,$q,$mode='single',$msg='');
                if(!$opt) return $out;
                return $rs['saleable'];	
        }
        
        
        
        public function get_sku_stock_list()
	{
		global $dbc;
                $out = array();
                $dealer_id=$_GET['id'];
                $date=$_GET['sale_date'];
		$out = NULL;
	        $q = "SELECT * FROM user_primary_sales_order upso INNER JOIN user_primary_sales_order_details upsod ON upsod.order_id=upso.order_id WHERE DATE_FORMAT(sale_date,'%Y-%d-%m')='$date' AND upso.dealer_id='$dealer_id' ";
  // h1($q);
                list($opt,$rs)= run_query($dbc,$q,$mode='multi',$msg='');
                $product_map = get_my_reference_array('catalog_product', 'id', 'name');
                if(!$opt) return $out;
                while($row = mysqli_fetch_assoc($rs)){
                            $id = $row['id'];
                            $out[$id] = $row;
                            $out[$id]['product'] = $product_map[$row['product_id']];
                            $out[$id]['product_qty'] = $this->get_product_qty($row['product_id'],$row['dealer_id'],$row['sale_date']);
                            $out[$id]['sale_product_qty'] = $this->get_sale_product_qty($row['product_id'],$row['dealer_id'],$row['sale_date']);
                    }
                    //  pre($out);exit;
                return $out;
              
        }
        public function get_product_qty($product_id,$dealer_id,$date)
	{
		global $dbc;
		$out = NULL;
	        $q = "SELECT SUM(quantity) as product_qty FROM user_primary_sales_order_details INNER JOIN user_primary_sales_order upso ON upso.order_id=user_primary_sales_order_details.order_id WHERE product_id='$product_id' AND dealer_id='$dealer_id' AND sale_date='$date'";
        // h1($q);
                list($opt,$rs)= run_query($dbc,$q,$mode='single',$msg='');
                if(!$opt) return $out;
                return $rs['product_qty'];	
        }
        public function get_sale_product_qty($product_id,$dealer_id,$date)
	{
		global $dbc;
		$out = NULL;
	        $q = "SELECT SUM(qty) as sale_product_qty FROM challan_order_details INNER JOIN challan_order  ON challan_order.id=challan_order_details.ch_id WHERE product_id='$product_id' AND ch_dealer_id='$dealer_id' AND DATE_FORMAT(ch_date,'%Y-%m-%d')='$date'";
       // h1($q);
                list($opt,$rs)= run_query($dbc,$q,$mode='single',$msg='');
                if(!$opt) return $out;
                return $rs['sale_product_qty'];	
        }
        public function get_balance_stock_report($filter='',  $records = '', $orderby='')
	{
		global $dbc;
		$out = array('software_generated'=>'', 'physical'=>'');	
                $sw_generated = array();
                $ph_generated = array();
		//if user has send some filter use them. invoice_date GROUP BY ItemId
		$filterstr = $this->oo_filter($filter, $records, $orderby);
		$q = "SELECT catalog_product_details.id AS uId, CONCAT_WS(' ', c2.name,cp.name,cp.unit, batch_no) AS pname,ostock FROM catalog_product_details INNER JOIN catalog_product cp ON cp.id = catalog_product_details.product_id INNER JOIN catalog_2 c2 ON c2.id = cp.catalog_id $filterstr";
                 h1($q);
		list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
		//$vendorId_map = get_my_reference_array('party', 'partyId', 'partyname');
		//$itemId = get_my_reference_array('item', 'itemId', 'itemname'); 
	        if($opt)
                {
                    while($row = mysqli_fetch_assoc($rs)){
                            $id = $row['uId'];
                            $sw_generated[$id] = $row;
                            //$out[$id]['partyId_val'] = $vendorId_map[$row['partyId']];
                    }
                }
                $q1 = "SELECT user_primary_sales_order_details.id,CONCAT_WS(' ', c2.name,cp.name,cp.unit, batch_no) AS pname,quantity FROM user_primary_sales_order_details INNER JOIN catalog_product cp ON cp.id = user_primary_sales_order_details.product_id INNER JOIN catalog_2 c2 ON c2.id = cp.catalog_id $filterstr";
              //  h1($q1);
                list($opt1, $rs1) = run_query($dbc, $q1, $mode='multi', $msg='');
                if($opt1)
                {
                     while($row = mysqli_fetch_assoc($rs1)){
                            $id = $row['id'];
                            $ph_generated[$id] = $row;
                    }
                }
		return array('software_generated'=>$sw_generated, 'physical'=>$ph_generated);
	}
        public function get_item_balance_stock_list($filter='',  $records = '', $orderby='')
	{
		global $dbc;
                $out = array();
		//if user has send some filter use them. invoice_date GROUP BY ItemId
		$filterstr = $this->oo_filter($filter, $records, $orderby);
                
		$q = "SELECT SUM(ostock) AS open_stock, CONCAT_WS(' ', c2.name,catalog_product.name,catalog_product.unit, batch_no) AS product_name,product_id FROM catalog_product_details INNER JOIN catalog_product ON catalog_product.id = catalog_product_details.product_id INNER JOIN catalog_2 c2 ON c2.id = catalog_product.catalog_id $filterstr";
               // h1($q);
		list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
		//$vendorId_map = get_my_reference_array('party', 'partyId', 'partyname');
		//$itemId = get_my_reference_array('item', 'itemId', 'itemname'); 
	        if($opt)
                {
                    while($row = mysqli_fetch_assoc($rs)){
                            $id = $row['product_id'];
                            $out[$id] = $row;
                            $out[$id]['sale_qty'] = myrowvaladvance('user_sales_order_details', 'SUM(quantity)', 'sale_qty', $where="product_id = '$id'");
                            //$out[$id]['partyId_val'] = $vendorId_map[$row['partyId']];
                    }
                }
              return $out;
	}
        public function get_sale_qty_list($filter='',  $records = '', $orderby='')
	{
		global $dbc;
                $out = array();
		//if user has send some filter use them. invoice_date GROUP BY ItemId
		$filterstr = $this->oo_filter($filter, $records, $orderby);
		$q = "SELECT * FROM user_sales_order_details INNER JOIN user_sales_order ON user_sales_order.order_id = user_sales_order_details.order_id $filterstr";
                //h1($q);
		list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
		$retailer_map = get_my_reference_array('retailer', 'id', 'name');
		$dealer_map = get_my_reference_array('dealer', 'id', 'name'); 
	        if($opt)
                {
                    while($row = mysqli_fetch_assoc($rs)){
                            $id = $row['order_id'];
                            $out[$id] = $row;
                            $out[$id]['rname'] = $retailer_map[$row['retailer_id']];
                            $out[$id]['dname'] = $dealer_map[$row['dealer_id']];
                    }
                }
              return $out;
	}
        public function get_primary_stock_list($filter='',  $records = '', $orderby='')
	{
		global $dbc;
                $out = array();
		//if user has send some filter use them. invoice_date GROUP BY ItemId
		$filterstr = $this->oo_filter($filter, $records, $orderby);
		$q = "SELECT *,DATE_FORMAT(mfg_date, '%d/%m/%Y') AS mfg_date, DATE_FORMAT(expiry_date, '%d/%m/%Y') AS expiry_date FROM catalog_product_details INNER JOIN catalog_product ON catalog_product.id = catalog_product_details.product_id $filterstr";
		list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
		$dealer_map = get_my_reference_array('dealer', 'id', 'name'); 
	        if($opt)
                {
                    $inc = 1;
                    while($row = mysqli_fetch_assoc($rs)){
                            $id = $inc;
                            $out[$id] = $row; 
                            $inc++;
                    }
                }
                
              return $out;
	}
        //This function is used to view party wise ledger report
        public function get_get_sorting_date_list($id)
	{
		global $dbc;
		$out = array();		
		//if user has send some filter use them. invoice_date GROUP BY ItemId
		$q = "SELECT DATE_FORMAT(ch_date,'%Y%m%d') AS ch_date FROM challan_order WHERE ch_retailer_id = '$id'";
             
		list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
		if(!$opt) return $date;
		while($row = mysqli_fetch_assoc($rs)){
			$out[$row['ch_date']] = $row['ch_date'];
		}
                $q1 = "SELECT DATE_FORMAT(payment_date,'%Y%m%d') AS payment_date FROM challan_order_wise_payment WHERE retailer_id = '$id'";
                list($opt1, $rs1) = run_query($dbc, $q1, $mode='multi', $msg='');
                if($opt1)
                {
                    while($rows = mysqli_fetch_assoc($rs1)){
			$out[$rows['payment_date']] = $rows['payment_date'];
                    }
                }
		return $out;
	}
        public function get_party_wise_ledger_report_list($rid)
	{
		global $dbc;
		$out = array();
                $date_list = $this->get_get_sorting_date_list($rid);
                if(!empty($date_list))
                {
                    foreach($date_list as $key=>$value)
                    {
                        $q = "SELECT SUM(product_rate * ch_qty) AS dr,challan_no, DATE_FORMAT(ch_date,'%d-%m-%Y') AS cdate  FROM challan_order INNER JOIN challan_order_details ON challan_order.ch_no = challan_order_details.challan_no  WHERE DATE_FORMAT(ch_date, '%Y%m%d') = '$value' AND ch_retailer_id = '$rid' GROUP BY challan_no ASC";            
                        list($opt, $rs) = run_query($dbc, $q,'multi');
                        if($opt)
                        {
                            $inc = 1;
                            while($row = mysqli_fetch_assoc($rs))
                            {
                                $id = $inc;
                                $out[$id][] = $row;
                                $inc++;
                            }
                        } // if($opt) end here
                    } //foreach($date_list as $key=>$value) end here
                   //pre($out);
                  foreach($date_list as $ikey=>$ivalue)
                   {  
                     $q1 = "SELECT pay_id AS challan_no, pay_amount AS cr,DATE_FORMAT(payment_date,'%d-%m-%Y') AS cdate FROM challan_order_wise_payment INNER JOIN challan_order_wise_payment_details USING(pay_id) WHERE DATE_FORMAT(payment_date, '%Y%m%d') = '$ivalue' AND retailer_id = '$rid' GROUP BY pay_id ASC"; 
                      //h1($q1);
                     list($opt1, $rs1) = run_query($dbc, $q1,'multi');
                        if($opt1)
                        {
                             $j = $inc;
                             while($row1 = mysqli_fetch_assoc($rs1))
                             {
                                 $id = $j;
                                 $out[$id][] = $row1;
                                 $j++;
                             }
                        }
                   } //foreach($date_list as $ikey=>$ivalue) end here
                } // if(!empty($date_list)) en here
                $final_array = array();
                $final_array_details = array();
                if(!empty($out))
                {
                    foreach($out as $inkey=>$invalue)
                    {
                       if(is_array($invalue))
                       {
                           $in = 1;
                           foreach($invalue as $inkey2=>$invalue2)
                           {
                              if(!isset($invalue2['cr'])) $invalue2['cr'] = 0;
                              if(!isset($invalue2['dr'])) $invalue2['dr'] = 0;
                              $final_array['challan_no'] = $invalue2['challan_no'];
                              $final_array['cdate'] = $invalue2['cdate'];
                              $final_array['cr'] = $invalue2['cr'];
                              $final_array['dr'] = $invalue2['dr'];
                              $final_array_details[] = $final_array;
                              $in++;
                           }
                       } // if(is_array($invalue)) end here
                    } //foreach($out as $inkey=>$invalue) end here
                } //if(!empty($out)) end here
                
		return $final_array_details;
	}
        
        public function get_total_payment_details($id, $date)
	{
		global $dbc;
		$out = NULL;
		$q = "SELECT SUM(pay_amount) AS pamt  FROM `challan_order_wise_payment` INNER JOIN challan_order_wise_payment_details USING(pay_id) WHERE challan_no = '$id' AND DATE_FORMAT(payment_date,'%d-%m-%Y') = '$date'";
               //h1($q);
		list($opt, $rs) = run_query($dbc, $q, $mode='single', $msg='');
		//$vendorId_map = get_my_reference_array('party', 'partyId', 'partyname');
		//$itemId = get_my_reference_array('item', 'itemId', 'itemname'); 
		if($opt) 
		   $out = $rs['pamt']; 
                return $out;
	}
        
        public function get_total_challan_value($id)
	{
		global $dbc;
		$out = NULL;
		$q = "SELECT SUM(product_rate * ch_qty) AS inrate FROM `challan_order_details` WHERE challan_no = '$id'";
                //h1($q);
		list($opt, $rs) = run_query($dbc, $q, $mode='single', $msg='');
		//$vendorId_map = get_my_reference_array('party', 'partyId', 'partyname');
		//$itemId = get_my_reference_array('item', 'itemId', 'itemname'); 
		if($opt) 
		 $out = $rs['inrate']; 
                return $out;
	}
}

?>