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
    public function get_balance_stock_report($filter='',  $records = '', $orderby='',$ch_filter='')
	{
		global $dbc;
		$out = array();	
		$filterstr = $this->oo_filter($filter, $records, $orderby);

                $dealer_id = $_SESSION[SESS.'data']['dealer_id'];
                $state_id  = $_SESSION[SESS.'data']['state_id'];

                /*$q1 = "SELECT catalog_view.product_id as pid,c1_id, r.retailer_rate AS rate,catalog_view.product_name, s.qty,s.nonsalable_damage as nonsale, r.mrp, r.dealer_rate FROM `catalog_view` LEFT JOIN `stock` s ON catalog_view.product_id=s.product_id AND dealer_id=$dealer_id LEFT JOIN product_rate_list r ON catalog_view.product_id=r.product_id  
                $filterstr AND state_id=$state_id ORDER BY catalog_view.c1_id,pid ASC";*/
                
                /*$q1 = "SELECT s.id as stock_id,c.c1_name as cat, c.product_id as pid,c.c1_id, s.rate ,c.product_name, s.qty,s.nonsalable_damage as nonsale, s.mrp, s.dealer_rate FROM `catalog_view` c LEFT JOIN `stock` s ON c.product_id=s.product_id AND s.dealer_id=$dealer_id $filterstr ORDER BY c.c1_id, pid ASC";*/
               /* $q1 = "SELECT s.batch_no,oc.cp_name AS company_name,oc.cp_short_name,s.id as stock_id,c.c2_name as cat, c.product_id as pid,c.c2_id AS c1_id, s.rate ,c.product_name, s.qty,s.nonsalable_damage as nonsale, s.mrp, s.dealer_rate, p.division,DATE_FORMAT(s.mfg,'%b-%Y') AS mfg FROM `catalog_view` c LEFT JOIN `stock` s ON c.product_id=s.product_id AND s.dealer_id=$dealer_id LEFT JOIN catalog_product p ON c.product_id=p.id LEFT JOIN other_company AS oc ON oc.id=p.company_id  WHERE s.dealer_id='$dealer_id' $filterstr GROUP BY s.dealer_id,s.product_id,s.batch_no,s.mfg ORDER BY c.c2_id, pid ASC";*/

            //    $q1 = "SELECT s.batch_no,oc.cp_name AS company_name,oc.cp_short_name,s.id as stock_id,c.c2_name as cat, c.product_id as pid,c.c2_id AS c1_id, s.rate ,c.product_name, s.qty,s.nonsalable_damage as nonsale, s.mrp, s.dealer_rate, p.division,DATE_FORMAT(s.mfg,'%b-%Y') AS mfg ,c.igst as product_gst FROM `catalog_view` c 
			//    LEFT JOIN `stock` s ON c.product_id=s.product_id AND s.dealer_id=$dealer_id 
			//    LEFT JOIN catalog_product p ON c.product_id=p.id 
			//    LEFT JOIN other_company AS oc ON oc.id=p.company_id  WHERE s.dealer_id='$dealer_id' $ch_filter GROUP BY s.dealer_id,s.product_id,s.batch_no,s.mfg ORDER BY c.c2_id, pid ASC";
                  $q1 = "SELECT s.batch_no,oc.cp_name AS company_name,oc.cp_short_name,s.id as stock_id,c.c2_name as cat, c.product_id as pid,c.itemcode as itemcode,c.c2_id AS c1_id, s.rate ,c.product_name, s.qty,s.nonsalable_damage as nonsale, s.mrp, s.dealer_rate, c.division,DATE_FORMAT(s.mfg,'%b-%Y') AS mfg ,c.igst as product_gst FROM `catalog_view` c 
			  LEFT JOIN `stock` s ON c.product_id=s.product_id AND s.dealer_id=$dealer_id 
			  LEFT JOIN other_company AS oc ON oc.id=c.c0_company_id  WHERE s.dealer_id='$dealer_id' $ch_filter GROUP BY s.dealer_id,s.product_id,s.batch_no,s.mfg ORDER BY c.c2_id, pid ASC";
              	// h1($q1);
                list($opt1, $rs1) = run_query($dbc, $q1, $mode='multi', $msg='');
                if($opt1)
                {
                     $i=0;
                     while($row = mysqli_fetch_assoc($rs1)){
                            $id = $row['pid'];
                            $mrp = $row['mrp'];
                            $out[$i] = $row;
                            // $out[$i]['cat'] = $row; 
                            // $out[$id]['manual'] = $this->manual_stock($id);
                            // $out[$id]['nonsale'] = $this->nonsale_stock($id);
                            $default_data = $this->get_product_mrp($id);
                            $out[$i]['product_mrp'] = $default_data['mrp'];
                            $out[$i]['default_dealer_rate'] = $default_data['d_rate'];
                           // $out[$i]['product_gst'] = $this->get_product_gst($id);
                            $out[$i]['intransit'] = $this->intransit_stock($id,$mrp);
                            $out[$i]['thresh'] = $this->thresold_stock($id);
                            $i++;
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
			$out = '';
	            $d_id =  $_SESSION[SESS.'data']['dealer_id'];
	            $s_id =  $_SESSION[SESS.'data']['state_id'];

		        $q1 = " SELECT mrp,dealer_rate FROM `product_rate_list` WHERE product_id='$id' AND state_id='$s_id' ";
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
	                         $out['mrp'] = $outr['mrp'];
	                         $out['d_rate'] = $outr['dealer_rate'];
	                    }
	                  
	                }
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
            public function intransit_stock($id,$mrp)
	{
		global $dbc;
                $d_id =  $_SESSION[SESS.'data']['dealer_id'];
	        $q1 = " SELECT sum(quantity) as qty,sum(scheme_qty) AS free FROM  `purchase_order_details` INNER JOIN purchase_order ON
                   purchase_order.order_id = purchase_order_details.order_id
                   GROUP by product_id,dealer_id
                    WHERE product_id='$id' AND dealer_id='$d_id' AND ch_date='1970-01-01' AND mrp=$mrp";
              // h1($q1);
                $outmg = mysqli_query($dbc, $q1);
                $outr = mysqli_fetch_assoc($outmg);
				$out1=$outr['qty']+$outr['free'];
		return $out1;
	}
           public function manual_stock($id)
	{
		global $dbc;
                $d_id =  $_SESSION[SESS.'data']['dealer_id'];
	        $q1 = " SELECT sum(qty) as qty FROM  `stock_manual` WHERE product_id='$id' AND dealer_id='$d_id' GROUP by product_id,dealer_id";
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
	        $q1 = " SELECT sum(qty) as qty FROM  `stock_nonsale` WHERE product_id='$id' AND dealer_id='$d_id' GROUP by product_id,dealer_id ";
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
	        $q1 = " SELECT sum(qty) as qty FROM  `threshold` WHERE product_id='$id' AND dealer_id='$d_id' GROUP BY product_id,dealer_id ";
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
	        //$q1 = " SELECT c1_name FROM  `catalog_view` WHERE product_id='$id'";
	        $q1 = " SELECT c3_name as c1_name FROM  `catalog_view` WHERE product_id='$id' GROUP BY c3_id";
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
$start = get_mysql_date($_POST['from_date'],'/',$time = false, $mysqlsearch = true);
$end = get_mysql_date($_POST['to_date'],'/',$time = false, $mysqlsearch = true);
$q1 = "SELECT COUNT(DISTINCT retailer_id) as tc ,COUNT(DISTINCT product_id) as total_sku,CONCAT_WS(' ',first_name,middle_name,last_name) as fullname, uso.dealer_id,uso.user_id,SUM(rate*quantity) as sale_value, "
. "(SELECT count(DISTINCT retailer_id) from user_sales_order uso1 where uso1.dealer_id = uso.dealer_id and uso.user_id=uso1.user_id AND call_status=1 AND DATE_FORMAT(`date`,'%Y%m%d')>='$start' AND DATE_FORMAT(`date`,'%Y%m%d')<='$end' ) as pc FROM `user_sales_order` uso"
. " INNER JOIN user_sales_order_details usod USING(order_id) INNER JOIN person on uso.user_id=person.id $filterstr ";
// h1($q1);
list($opt1, $rs1) = run_query($dbc, $q1, $mode='multi', $msg='');
if($opt1)
{
while($row = mysqli_fetch_assoc($rs1)){
$id = $row['user_id']; 
$out[$id] = $row; 
//$cond="user_id=".$row['user_id']." AND call_status='1'";
//$out[$id]['pc']=myrowval('user_sales_order','count(DISTINCT order_id)',$cond);
}
}
return $out;
}
public function get_user_bill_performance_list($filter='', $records = '', $orderby='',$ch_filter)
{
global $dbc;
$out = array();
$filterstr = $this->oo_filter($filter, $records, $orderby);
$q1 = "SELECT CONCAT_WS(' ',first_name,middle_name,last_name) as fullname, co.ch_dealer_id,co.ch_user_id AS user_id,SUM(amount) as sale_value "
." FROM `challan_order` co"
. " INNER JOIN person on co.ch_user_id=person.id $filterstr ";
 //h1($q1);
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
$q1 = "SELECT COUNT(DISTINCT product_id) as total_sku,retailer.name as retailer_name, uso.dealer_id,uso.user_id,SUM(rate*quantity) as sale_value,uso.retailer_id "
. " FROM `user_sales_order` uso"
. " INNER JOIN user_sales_order_details usod USING(order_id) INNER JOIN retailer on uso.retailer_id=retailer.id $filterstr ";
 //h1($q1);
list($opt1, $rs1) = run_query($dbc, $q1, $mode='multi', $msg='');
if($opt1)
{
while($row = mysqli_fetch_assoc($rs1)){
$id = $row['retailer_id']; 
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


    public function stock_delete($id, $filter='', $records='', $orderby='')
    {
    global $dbc;
    if(empty($filter)) $filter = "stock.id = '$id'";
    $out = array('status'=>false, 'myreason'=>'');
    $delsq="SELECT id FROM stock WHERE `id`='$id' ";
    $runsq= mysqli_query($dbc, $delsq);
    $deleteRecord= mysqli_num_rows($runsq);
    if($deleteRecord==0){ $out['myreason'] = 'stock not found'; return $out;}
    //start the transaction
    mysqli_query($dbc, "START TRANSACTION");

    //Running the deletion queries
    $delquery = array();
    $delquery['stock'] = "DELETE FROM `stock` WHERE `id`='$id' ";

    foreach($delquery as $key=>$value){
    if(!mysqli_query($dbc, $value)){
    mysqli_rollback($dbc);
    return array('status'=>false, 'myreason'=>'$key query failed');
    }
    }
    //After successfull deletion
    mysqli_commit($dbc);
    return array('status'=>true, 'myreason'=>'stock');
    }
public function get_retailer_dues_list($filter='', $records = '', $orderby='',$ch_filter)
{
global $dbc;
$out = array(); 
$filterstr = $this->oo_filter($filter, $records, $orderby);
$start = get_mysql_date($_POST['from_date'],'/',$time = false, $mysqlsearch = true);
$start=date('Ymd');
$date30 = date('Ymd', strtotime($start . ' -30 days'));
$date60 = date('Ymd', strtotime($start . ' -60 days'));
$date90 = date('Ymd', strtotime($start . ' -90 days'));
//h1($date60);
$q1 = "SELECT retailer.name as retailer_name,SUM(co.remaining) as sale_value,co.ch_retailer_id "
. " FROM `challan_order` co"
. "  INNER JOIN retailer on co.ch_retailer_id=retailer.id $filterstr ";
 //h1($q1);
$rs1=mysqli_query($dbc,$q1);

while($row = mysqli_fetch_assoc($rs1)){
$id = $row['ch_retailer_id']; 
$out[$id] = $row;
$where30="ch_retailer_id='$row[ch_retailer_id]' AND DATE_FORMAT(`ch_date`,'%Y%m%d')>='".$date30."' AND DATE_FORMAT(`ch_date`,'%Y%m%d')<'".$start."'";
$out[$id]['lt30']=myrowval('challan_order','SUM(remaining)',$where30);
$where60="ch_retailer_id='$row[ch_retailer_id]' AND DATE_FORMAT(`ch_date`,'%Y%m%d')>='".$date60."' AND DATE_FORMAT(`ch_date`,'%Y%m%d')<'".$date30."'";
$out[$id]['lt60']=myrowval('challan_order','SUM(remaining)',$where60);
$where90="ch_retailer_id='$row[ch_retailer_id]' AND DATE_FORMAT(`ch_date`,'%Y%m%d')>='".$date90."' AND DATE_FORMAT(`ch_date`,'%Y%m%d')<'".$date60."'";
$out[$id]['lt90']=myrowval('challan_order','SUM(remaining)',$where90);
$where90p="ch_retailer_id='$row[ch_retailer_id]' AND DATE_FORMAT(`ch_date`,'%Y%m%d')<'".$date90."'";
$out[$id]['lt90plus']=myrowval('challan_order','SUM(remaining)',$where90p);
}
return $out;
} 
public function get_sales_purchase_performance_list($filter='', $records = '', $orderby='',$ch_filter)
{
global $dbc;
$out = array(); 
$filterstr = $this->oo_filter($filter, $records, $orderby);
$start = get_mysql_date($_POST['from_date'],'/',$time = false, $mysqlsearch = true);
$end = get_mysql_date($_POST['to_date'],'/',$time = false, $mysqlsearch = true);
$dea_id=$_SESSION[SESS.'data']['dealer_id'];
$start1    = (new DateTime($start))->modify('first day of this month');
$end1     = (new DateTime($end))->modify('first day of next month');
$interval = DateInterval::createFromDateString('1 month');
$period   = new DatePeriod($start1, $interval, $end1);
foreach ($period as $dt) {
	$id=$dt->format("Y-m");
	$mon=$dt->format("M-Y");
    $out[$id][]=$mon;
    $wherec="DATE_FORMAT(`ch_date`,'%Y-%m')='".$id."' AND ch_dealer_id=".$dea_id; 
    $sale=myrowval('challan_order','sum(amount_round)',$wherec);
    $out[$id]['sale']=$sale;
    $wherep="DATE_FORMAT(`created_date`,'%Y-%m')='".$id."' AND dealer_id=".$dea_id; 
    $purchase=myrowvaljoin('purchase_order','sum(total_amount)','purchase_order_details','purchase_order.order_id=purchase_order_details.order_id AND purchase_order.challan_no=purchase_order_details.purchase_inv',$wherep);
    $out[$id]['purchase']=$purchase;
}
//pre($out);
return $out;
}

public function get_sales_purchase_list($filter='', $records = '', $orderby='',$ch_filter)
{
global $dbc;
$out = array(); 
$filterstr = $this->oo_filter($filter, $records, $orderby);
$dea_id=$_SESSION[SESS.'data']['dealer_id'];
$q="SELECT c3_id AS pid,c3_name AS cat FROM catalog_view AS cv INNER JOIN challan_order_details AS cod ON cod.product_id=cv.product_id INNER JOIN challan_order AS co ON co.id=cod.ch_id INNER JOIN catalog_3 ON catalog_3.id=cv.c3_id WHERE co.ch_dealer_id='$dea_id' AND c3_id NOT IN(2,8) GROUP BY pid ORDER BY seq_id";
$r=mysqli_query($dbc,$q);
while($row=mysqli_fetch_assoc($r)){
$id=$row['pid'];
$out[$id]=$row;
}
//pre($out);
return $out;
}
        public function get_purchase_stock($cid,$start,$end)
	{
		global $dbc;
		$out = array();
		$dea_id=$_SESSION[SESS.'data']['dealer_id'];
		$q = "SELECT ROUND(sum(total_amount),2) AS purchase FROM purchase_order_details INNER JOIN purchase_order ON purchase_order_details.order_id=purchase_order.order_id AND purchase_order.challan_no=purchase_order_details.purchase_inv INNER JOIN catalog_view ON purchase_order_details.product_id=catalog_view.product_id WHERE catalog_view.c3_id='$cid' AND DATE_FORMAT(created_date,'%Y%m%d')>='$start' AND DATE_FORMAT(created_date,'%Y%m%d')<='$end' AND dealer_id='$dea_id'";
		//h1($q);
		$r=mysqli_query($dbc,$q);
		$rs=mysqli_fetch_assoc($r);
		return $rs['purchase'];	
	}
	public function get_billed_stock($cid,$start,$end)
	{
		global $dbc;
		$out = array();
		$dea_id=$_SESSION[SESS.'data']['dealer_id'];
		$q = "SELECT ROUND(sum(taxable_amt-vat_amt),2) AS billed FROM challan_order_details INNER JOIN challan_order ON challan_order_details.ch_id=challan_order.id INNER JOIN catalog_view ON challan_order_details.product_id=catalog_view.product_id WHERE catalog_view.c3_id='$cid' AND DATE_FORMAT(ch_date,'%Y%m%d')>='$start' AND DATE_FORMAT(ch_date,'%Y%m%d')<='$end' AND ch_dealer_id='$dea_id'";
		//h1($q);
		$r=mysqli_query($dbc,$q);
		$rs=mysqli_fetch_assoc($r);
		return $rs['billed'];	
	}
	public function get_order_stock($cid,$start,$end)
	{
		global $dbc;
		$out = array();
		$dea_id=$_SESSION[SESS.'data']['dealer_id'];
		$q = "SELECT ROUND(sum(quantity*rate),2) AS ordered FROM user_sales_order_details INNER JOIN user_sales_order ON user_sales_order_details.order_id=user_sales_order.order_id INNER JOIN catalog_view ON user_sales_order_details.product_id=catalog_view.product_id WHERE catalog_view.c3_id='$cid' AND DATE_FORMAT(date,'%Y%m%d')>='$start' AND DATE_FORMAT(date,'%Y%m%d')<='$end' AND dealer_id='$dea_id'";
		//h1($q);
		$r=mysqli_query($dbc,$q);
		$rs=mysqli_fetch_assoc($r);
		return $rs['ordered'];	
	}
        public function get_purchase_stock_ctr($cid,$start,$end)
	{
		global $dbc;
		$out = array();
		$dea_id=$_SESSION[SESS.'data']['dealer_id'];
		$q = "SELECT ROUND(sum(quantity+scheme_qty)) AS pqty,unit AS punit FROM purchase_order_details INNER JOIN purchase_order ON purchase_order_details.order_id=purchase_order.order_id AND purchase_order.challan_no=purchase_order_details.purchase_inv INNER JOIN catalog_view ON purchase_order_details.product_id=catalog_view.product_id WHERE catalog_view.c3_id='$cid' AND DATE_FORMAT(created_date,'%Y%m%d')>='$start' AND DATE_FORMAT(created_date,'%Y%m%d')<='$end' AND dealer_id='$dea_id' GROUP BY catalog_view.product_id";
		//h1($q);
		$r=mysqli_query($dbc,$q);
		while($rs=mysqli_fetch_assoc($r))
		{
			$ctr=($rs['pqty']/$rs['punit']);
			$out[]=$ctr;
		}
		$pctr=ROUND(array_sum($out));
		return $pctr;		
	}
	public function get_billed_stock_ctr($cid,$start,$end)
	{
		global $dbc;
		$out = array();
		$dea_id=$_SESSION[SESS.'data']['dealer_id'];
		$q = "SELECT ROUND(sum(qty+free_qty)) AS bqty,unit AS bunit FROM challan_order_details INNER JOIN challan_order ON challan_order_details.ch_id=challan_order.id INNER JOIN catalog_view ON challan_order_details.product_id=catalog_view.product_id WHERE catalog_view.c3_id='$cid' AND DATE_FORMAT(ch_date,'%Y%m%d')>='$start' AND DATE_FORMAT(ch_date,'%Y%m%d')<='$end' AND ch_dealer_id='$dea_id' GROUP BY catalog_view.product_id";
		//h1($q);
		$r=mysqli_query($dbc,$q);
		while($rs=mysqli_fetch_assoc($r))
		{
			$ctr=($rs['bqty']/$rs['bunit']);
			$out[]=$ctr;
		}
		$bctr=ROUND(array_sum($out),2);
		return $bctr;	
	}
    	public function get_order_stock_ctr($cid,$start,$end)
	{
		global $dbc;
		$out = array();
		$dea_id=$_SESSION[SESS.'data']['dealer_id'];
		$q = "SELECT ROUND(sum(quantity)) AS orderqty,unit AS orderunit FROM user_sales_order_details INNER JOIN user_sales_order ON user_sales_order_details.order_id=user_sales_order.order_id INNER JOIN catalog_view ON user_sales_order_details.product_id=catalog_view.product_id WHERE catalog_view.c3_id='$cid' AND DATE_FORMAT(date,'%Y%m%d')>='$start' AND DATE_FORMAT(date,'%Y%m%d')<='$end' AND dealer_id='$dea_id' GROUP BY catalog_view.product_id";
		//h1($q);
		$r=mysqli_query($dbc,$q);
		while($rs=mysqli_fetch_assoc($r))
		{
			$ctr=($rs['orderqty']/$rs['orderunit']);
			$out[]=$ctr;
		}
		$bctr=ROUND(array_sum($out),2);
		return $bctr;	
	}    
	public function get_rbilled_stock($cid,$rid,$start,$end)
	{
		global $dbc;
		$out = array();
		$dea_id=$_SESSION[SESS.'data']['dealer_id'];
		$q = "SELECT ROUND(sum(taxable_amt-vat_amt),2) AS billed FROM challan_order_details INNER JOIN challan_order ON challan_order_details.ch_id=challan_order.id INNER JOIN catalog_view ON challan_order_details.product_id=catalog_view.product_id WHERE catalog_view.c3_id='$cid' AND DATE_FORMAT(ch_date,'%Y%m%d')>='$start' AND DATE_FORMAT(ch_date,'%Y%m%d')<='$end' AND ch_dealer_id='$dea_id' AND ch_retailer_id='$rid'";
		//h1($q);
		$r=mysqli_query($dbc,$q);
		$rs=mysqli_fetch_assoc($r);
		return $rs['billed'];	
	}
	public function get_rbilled_stock_ctr($cid,$rid,$start,$end)
	{
		global $dbc;
		$out = array();
		$dea_id=$_SESSION[SESS.'data']['dealer_id'];
		$q = "SELECT ROUND(sum(qty+free_qty)) AS bqty,unit AS bunit FROM challan_order_details INNER JOIN challan_order ON challan_order_details.ch_id=challan_order.id INNER JOIN catalog_view ON challan_order_details.product_id=catalog_view.product_id WHERE catalog_view.c3_id='$cid' AND DATE_FORMAT(ch_date,'%Y%m%d')>='$start' AND DATE_FORMAT(ch_date,'%Y%m%d')<='$end' AND ch_dealer_id='$dea_id' AND ch_retailer_id='$rid' GROUP BY catalog_view.product_id";
		//h1($q);
		$r=mysqli_query($dbc,$q);
		while($rs=mysqli_fetch_assoc($r))
		{
			$ctr=($rs['bqty']/$rs['bunit']);
			$out[]=$ctr;
		}
		$bctr=ROUND(array_sum($out),2);
		return $bctr;	
	}
	public function get_rorder_stock($cid,$rid,$start,$end)
	{
		global $dbc;
		$out = array();
		$dea_id=$_SESSION[SESS.'data']['dealer_id'];
		$q = "SELECT ROUND(sum(quantity*rate),2) AS ordered FROM user_sales_order_details INNER JOIN user_sales_order ON user_sales_order_details.order_id=user_sales_order.order_id INNER JOIN catalog_view ON user_sales_order_details.product_id=catalog_view.product_id WHERE catalog_view.c3_id='$cid' AND DATE_FORMAT(date,'%Y%m%d')>='$start' AND DATE_FORMAT(date,'%Y%m%d')<='$end' AND dealer_id='$dea_id' AND retailer_id='$rid'";
		//h1($q);
		$r=mysqli_query($dbc,$q);
		$rs=mysqli_fetch_assoc($r);
		return $rs['ordered'];	
	}
	public function get_rorder_stock_ctr($cid,$rid,$start,$end)
	{
		global $dbc;
		$out = array();
		$dea_id=$_SESSION[SESS.'data']['dealer_id'];
		$q = "SELECT ROUND(sum(quantity)) AS bqty,unit AS bunit FROM user_sales_order_details INNER JOIN user_sales_order ON user_sales_order_details.order_id=user_sales_order.order_id INNER JOIN catalog_view ON user_sales_order_details.product_id=catalog_view.product_id WHERE catalog_view.c3_id='$cid' AND DATE_FORMAT(date,'%Y%m%d')>='$start' AND DATE_FORMAT(date,'%Y%m%d')<='$end' AND dealer_id='$dea_id' AND retailer_id='$rid' GROUP BY catalog_view.product_id";
		//h1($q);
		$r=mysqli_query($dbc,$q);
		while($rs=mysqli_fetch_assoc($r))
		{
			$ctr=($rs['bqty']/$rs['bunit']);
			$out[]=$ctr;
		}
		$bctr=ROUND(array_sum($out),2);
		return $bctr;	
	}
public function get_product_sale_list($filter='', $records = '', $orderby='',$ch_filter)
{
global $dbc;
$out = array(); 
$filterstr = $this->oo_filter($filter, $records, $orderby);
$dea_id=$_SESSION[SESS.'data']['dealer_id'];
$q="SELECT cod.id AS cid,r.id AS rid,r.name,cv.product_id AS pid,cv.product_name,ROUND(sum(cod.qty+cod.free_qty)) AS qty,sum(taxable_amt-vat_amt) AS taxable_amt,unit FROM catalog_view AS cv INNER JOIN challan_order_details AS cod ON cod.product_id=cv.product_id INNER JOIN challan_order AS co ON co.id=cod.ch_id INNER JOIN retailer AS r ON r.id=co.ch_retailer_id $filterstr  GROUP BY rid,pid ";
//h1($q);
$r=mysqli_query($dbc,$q);
while($row=mysqli_fetch_assoc($r)){
$id=$row['cid'];
if($row['unit']==0){
$box=0;
}else{
$box=$row['qty']/$row['unit'];	
}
$out[$id]=$row;
$out[$id]['box']=ROUND($box,2);
}
//pre($out);
return $out;
}

public function get_retailer_payment_list($filter='', $records = '', $orderby='',$ch_filter)
{
global $dbc;
$out = array(); 
$filterstr = $this->oo_filter($filter, $records, $orderby);
$dea_id=$_SESSION[SESS.'data']['dealer_id'];
$q="SELECT pc.id AS pid,r.id AS rid,r.name AS name,total_amount AS amount,DATE_FORMAT(`pay_date_time`,'%d-%b-%Y') AS rdate FROM payment_collection AS pc INNER JOIN retailer AS r ON r.id=pc.retailer_id $filterstr  ORDER BY name,rdate";
//h1($q);
$r=mysqli_query($dbc,$q);
while($row=mysqli_fetch_assoc($r)){
$id=$row['pid'];
if($row['unit']==0){
$box=0;
}else{
$box=$row['qty']/$row['unit'];	
}
$out[$id]=$row;
$out[$id]['box']=ROUND($box,2);
}
//pre($out);
return $out;
}

}

?>

