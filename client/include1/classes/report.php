<?php
class report extends myfilter
{
	
	function __construct()
	{
		parent::__construct();
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
    public function get_balance_stock_report($filter='',  $records = '', $orderby='',$ch_filter)
	{
		global $dbc;
		$out = array();	
		$filterstr = $this->oo_filter($filter, $records, $orderby);

                $dealer_id = $_SESSION[SESS.'data']['dealer_id'];
                $state_id  = $_SESSION[SESS.'data']['state_id'];

                /*$q1 = "SELECT catalog_view.product_id as pid,c1_id, r.retailer_rate AS rate,catalog_view.product_name, s.qty,s.nonsalable_damage as nonsale, r.mrp, r.dealer_rate FROM `catalog_view` LEFT JOIN `stock` s ON catalog_view.product_id=s.product_id AND dealer_id=$dealer_id LEFT JOIN product_rate_list r ON catalog_view.product_id=r.product_id  
                $filterstr AND state_id=$state_id ORDER BY catalog_view.c1_id,pid ASC";*/
                
                $q1 = "SELECT c.c1_name as cat, c.product_id as pid,c.c1_id, s.rate ,c.product_name, s.qty,s.nonsalable_damage as nonsale, s.mrp, s.dealer_rate FROM `catalog_view` c LEFT JOIN `stock` s ON c.product_id=s.product_id AND s.dealer_id=$dealer_id $filterstr ORDER BY c.c1_id, pid ASC";
               	// h1($q1);
                list($opt1, $rs1) = run_query($dbc, $q1, $mode='multi', $msg='');
                if($opt1)
                {
                     $i=0;
                     while($row = mysqli_fetch_assoc($rs1)){
                            $id = $row['pid'];
                            $out[$i] = $row;
                            // $out[$i]['cat'] = $row; 
                            // $out[$id]['manual'] = $this->manual_stock($id);
                            // $out[$id]['nonsale'] = $this->nonsale_stock($id);
                            $out[$i]['product_mrp'] = $this->get_product_mrp($id);
                            $out[$i]['product_gst'] = $this->get_product_gst($id);
                            $out[$i]['intransit'] = $this->intransit_stock($id);
                            $out[$i++]['thresh'] = $this->thresold_stock($id);
                    }
                }
              
		return $out;
	}

	    public function get_product_gst($pid)
	    {
	    	global $dbc;
	    	$q1 = " SELECT igst FROM `_gst` g LEFT JOIN `catalog_product` p ON g.hsn_code=p.hsn_code WHERE p.id='$pid' ";
	    	
	    	$outmg = mysqli_query($dbc, $q1);
	    	$outr = mysqli_fetch_assoc($outmg);

	    	return ($outr['igst']) ? $outr['igst'] : 0;
	    }

	    public function get_product_mrp($id)
		{
			global $dbc;
	            $d_id =  $_SESSION[SESS.'data']['dealer_id'];
	            $s_id =  $_SESSION[SESS.'data']['state_id'];

		        $q1 = " SELECT mrp FROM  `product_rate_list` WHERE product_id='$id' AND state_id='$s_id' ";
	               // h1($q1);
	                
	                $outmg = mysqli_query($dbc, $q1);
	                while($outr = mysqli_fetch_assoc($outmg))
	                {
	                    if(empty($outr['mrp']))
	                    {
	                         $out = 0;
	                    }
	                    else
	                    {
	                         $out = $outr['mrp'];
	                    }
	                  
	                }
	              // print_r($out);
			return $out;
		}
        public function get_available_stock($pid,$dealer_id)
	{
		global $dbc;
		$out = array();	
		$filterstr = $this->oo_filter($filter, $records, $orderby);
                $q1 = "SELECT pid,dealer_available_stock.product_name as product_name,rate,balance_stock,salable_stock,non_salable_stock FROM `dealer_available_stock` 
                    INNER JOIN catalog_view ON catalog_view.product_id=dealer_available_stock.pid 
                    WHERE dealer_id = '$dealer_id' AND pid = '$pid'";
               //h1($q1);
               // $product = array();
                list($opt1, $rs1) = run_query($dbc, $q1, $mode='multi', $msg='');
                if($opt1)
                {
                     while($row = mysqli_fetch_assoc($rs1)){
                            $id = $row['pid'];                        
                            $out[$id] = $row;	
                            $out[$id]['cat'] = $this->category($id); 
                            $out[$id]['manual'] = $this->manual_stock($id);
                            $out[$id]['nonsale'] = $this->nonsale_stock($id);
                            $out[$id]['intransit'] = $this->intransit_stock($id);
                            $out[$id]['thresh'] = $this->thresold_stock($id);
                    }
                }
              
              // pre($out);
		return $out;
	}
            public function intransit_stock($id)
	{
		global $dbc;
                $d_id =  $_SESSION[SESS.'data']['dealer_id'];
	        $q1 = " SELECT sum(quantity) as qty FROM  `purchase_order_details` INNER JOIN purchase_order ON
                   purchase_order.order_id = purchase_order_details.order_id
                    WHERE product_id='$id' AND dealer_id='$d_id' AND ch_date='1970-01-01'";
              // h1($q1);
                $outmg = mysqli_query($dbc, $q1);
                $outr = mysqli_fetch_assoc($outmg);
		return $outr['qty'];
	}
           public function manual_stock($id)
	{
		global $dbc;
                $d_id =  $_SESSION[SESS.'data']['dealer_id'];
	        $q1 = " SELECT sum(qty) as qty FROM  `stock_manual` WHERE product_id='$id' AND dealer_id='$d_id' ";
                //h1($q1);
                $outmg = mysqli_query($dbc, $q1);
                while($outr = mysqli_fetch_assoc($outmg))
                {
                    if(empty($outr['qty']))
                    {
                         $out = 0;
                    }
                    else
                    {
                         $out = $outr['qty'];
                    }
                  
                }
              // print_r($out);
		return $out;
	}
        public function nonsale_stock($id)
	{
		global $dbc;
                $d_id =  $_SESSION[SESS.'data']['dealer_id'];
	        $q1 = " SELECT sum(qty) as qty FROM  `stock_nonsale` WHERE product_id='$id' AND dealer_id='$d_id' ";
               // h1($q1);
                
                $outmg = mysqli_query($dbc, $q1);
                while($outr = mysqli_fetch_assoc($outmg))
                {
                    if(empty($outr['qty']))
                    {
                         $out = 0;
                    }
                    else
                    {
                         $out = $outr['qty'];
                    }
                  
                }
              // print_r($out);
		return $out;
	}
         public function thresold_stock($id)
	{
		global $dbc;
                $d_id =  $_SESSION[SESS.'data']['dealer_id'];
	        $q1 = " SELECT sum(qty) as qty FROM  `threshold` WHERE product_id='$id' AND dealer_id='$d_id' ";
               // h1($q1);
                $outmg = mysqli_query($dbc, $q1);
                while($outr = mysqli_fetch_assoc($outmg))
                {
                    if(empty($outr['qty']))
                    {
                         $out = 0;
                    }
                    else
                    {
                         $out = $outr['qty'];
                    }
                  
                }
              // print_r($out);
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
        
        public function product_rate($id)
	{
		global $dbc;
                  $state = $_SESSION[SESS . 'data']['state_id'];
	        $q1 = " SELECT dealer_rate as rate FROM  `product_rate_list` WHERE product_id='$id' AND state_id ='$state'";
              // h1($q1);
                $outmg = mysqli_query($dbc, $q1);
                while($outr = mysqli_fetch_assoc($outmg))
                {
                   $out = $outr['rate'];
                }
              // print_r($out);
		return $out;
	}
        
        ###### Balance Stock Edit ###########################
        public function get_balance_stock_edit($filter='',  $records = '', $orderby='',$ch_filter)
	{
		global $dbc;
		$out = array();		
		$filterstr = $this->oo_filter($filter, $records, $orderby);
                $q1 = "  SELECT * FROM `dealer_available_stock` $filterstr  ";
            //h1($q1);
                list($opt1, $rs1) = run_query($dbc, $q1, $mode='multi', $msg='');
                if($opt1)
                {
                     while($row = mysqli_fetch_assoc($rs1)){
                            $id = $row['pid'];                            
                            $out[$id] = $row;
                            $out[$id]['cat'] = $this->category($id); 
                    }
                }
                //$this->get_damage_stock_id();
               // pre($out);
		return $out;
	}
        
        
         public function get_damage_stock_id()
	{
		global $dbc;
		$out = array();	
		$filterstr = $this->oo_filter($filter, $records, $orderby);
                $q1 = "  SELECT `damage_order_details`.`id` FROM `damage_order_details`
                         INNER JOIN `catalog_product` ON `catalog_product`.`id`=  `damage_order_details`.`product_id`";
            //h1($q1);
                list($opt1, $rs1) = run_query($dbc, $q1, $mode='multi', $msg='');
                if($opt1)
                {
                     while($row = mysqli_fetch_assoc($rs1)){
                            $id = $row['id'];                            
                            $out[$id] = $row;								
                    }
                }
                //pre($out);
		return $out;
	}
        
        
        public function get_item_balance_stock_list($filter='',  $records = '', $orderby='')
	{
		global $dbc;
                $out = array();
		//if user has send some filter use them. invoice_date GROUP BY ItemId
		$filterstr = $this->oo_filter($filter, $records, $orderby);
                
		$q = "SELECT SUM(ostock) AS open_stock, CONCAT_WS(' ', name, batch_no) AS product_name,product_id FROM catalog_product_details INNER JOIN catalog_product ON catalog_product.id = catalog_product_details.product_id $filterstr";
                //h1($q);
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
public function get_user_sales_performance_list($filter='', $records = '', $orderby='',$ch_filter)
{
global $dbc;
$out = array(); 
$filterstr = $this->oo_filter($filter, $records, $orderby);
$q1 = "SELECT COUNT(DISTINCT retailer_id) as tc ,COUNT(DISTINCT product_id) as total_sku,CONCAT_WS(' ',first_name,middle_name,last_name) as fullname, uso.dealer_id,uso.user_id,SUM(rate*quantity) as sale_value, "
. "(SELECT count(DISTINCT retailer_id) from user_sales_order uso1 where uso1.dealer_id = uso.dealer_id and uso.user_id=uso1.user_id AND call_status=1) as pc FROM `user_sales_order` uso"
. " INNER JOIN user_sales_order_details usod USING(order_id) INNER JOIN person on uso.user_id=person.id $filterstr ";
// h1($q1);
list($opt1, $rs1) = run_query($dbc, $q1, $mode='multi', $msg='');
if($opt1)
{
while($row = mysqli_fetch_assoc($rs1)){
$id = $row['user_id']; 
$out[$id] = $row; 
}
}
return $out;
}


public function get_retailer_sales_performance_list($filter='', $records = '', $orderby='',$ch_filter)
{
global $dbc;
$out = array(); 
$filterstr = $this->oo_filter($filter, $records, $orderby);
$q1 = "SELECT COUNT(DISTINCT product_id) as total_sku,retailer.name as retailer_name, uso.dealer_id,uso.user_id,SUM(rate*quantity) as sale_value "
. " FROM `user_sales_order` uso"
. " INNER JOIN user_sales_order_details usod USING(order_id) INNER JOIN retailer on uso.retailer_id=retailer.id $filterstr ";
// h1($q1);
list($opt1, $rs1) = run_query($dbc, $q1, $mode='multi', $msg='');
if($opt1)
{
while($row = mysqli_fetch_assoc($rs1)){
$id = $row['user_id']; 
$out[$id] = $row; 
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
        
        
         public function get_balance_se_data() {
        $d1 = array();
        $d1 = $_POST;
        $d1['uid'] = $_SESSION[SESS . 'id'];
        //$d1['csess'] = $_SESSION[SESS.'csess'];
        $d1['myreason'] = 'Please fill all the required information';
        $d1['what'] = 'Catalog Product'; //whether to do history log or not
        return array(true, $d1);
    }

        
}

?>
