<?php
class front_end_sale
{
	private $id = NULL;
	private $id_detail = NULL;
	private $catalgouevendor = NULL;
	
	function __construct($id)
	{
		$this->id = $id;
	}
	// This function will get the vendor name provided the catalog id
	public function get_catalog_vendor_name($clId)
	{
		global $dbc;
		$out = NULL;
		$q= "SELECT isvId, name, code FROM item_source_vendor INNER JOIN catalogue USING (isvId) WHERE catalogue.clId = '$clId'";
		list($opt, $rs) = run_query($dbc, $q, $mode='single', $msg='');
		if($opt)
		{
			$out['name'] = $rs['name'];	
			$out['code'] = $rs['code'];
			$out['isvId'] = $rs['isvId'];		
		}// if($opt) ends
		return $out;
	}
	// This function will bring the list of the vendor whose items are to be listed for sale in the front end
	public function get_catalogue_vendor_list()
	{
		global $dbc;
		$out = NULL;
		$q= "SELECT name,code, isvId FROM item_source_vendor INNER JOIN catalogue USING (isvId) ORDER BY name ASC";
		list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
		if($opt)
		{
			while($row = mysqli_fetch_assoc($rs))
			{				
				$out[$row['isvId']]['code'] = $row['code'];
				$out[$row['isvId']]['name'] = $row['name'];
			}//while loop ends			
		}// if($opt) ends
		$this->cataloguevendor = $out;
		return $out;
	}
	// This will give the various catalogue belonging to a given vendor id
	public function get_catalogue_by_vendor($id)
	{
		global $dbc;
		$out = NULL;
		$q= "SELECT clId, cl_no FROM catalogue WHERE isvId = '$id' ORDER BY cl_no ASC";
		list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
		if($opt)
		{
			while($row = mysqli_fetch_assoc($rs))
			{				
				$out[$row['clId']] = $row['cl_no'];
			}//while loop ends			
		}// if($opt) ends
		//$this->cataloguevendor = $out;
		return $out;
	}
	
	//This function will fetch randomly an image from a given catalgoue id to showin front end
	public function get_catalogue_image($id)
	{
		global $dbc;
		$out = 'no-catalogue-cover.jpg';
		$q= "SELECT filename, itemId FROM items WHERE clId = '$id' AND filename != '' LIMIT 1";
		list($opt, $rs) = run_query($dbc, $q, $mode='single', $msg='');
		if($opt)
		{
			/*if(is_file('./admin/item-images/'.$rs['filename'])) // when calling from front end
				$out = $rs['filename'];
			elseif(is_file('./item-images/'.$rs['filename'])) // when called from within the admin panel
				$out = $rs['filename'];
			else*/
				$out = $rs['filename'];
		}// if($opt) ends
		return $out;
	}
	
	public function get_catalogue_by_series($id, $cl_no)
	{
		global $dbc;
		$out = NULL;
		$q= "SELECT clId,cl_series FROM catalogue WHERE isvId = '$id' AND cl_no='$cl_no' ORDER BY cl_series ASC";
		list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
		if($opt)
		{
			while($row = mysqli_fetch_assoc($rs))
			{				
				$out[$row['clId']]['series'] = $row['cl_series'];
			}//while loop ends			
		}// if($opt) ends
		//$this->cataloguevendor = $out;
		return $out;
	}
	
	public function get_item_by_catalogue($clId)
	{
		global $dbc;
		$out = NULL;
		$q= "SELECT itemId, salecode, purchasecode, filename, sale_price FROM items WHERE clId = '$clId' ORDER BY salecode ASC";
		list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
		if($opt)
		{
			while($row = mysqli_fetch_assoc($rs))
			{				
				$out[$row['itemId']]['purchasecode'] = $row['purchasecode'];
				$out[$row['itemId']]['salecode'] = $row['salecode'];
				$out[$row['itemId']]['image'] = $row['filename'];
				$out[$row['itemId']]['saleprice'] = $row['sale_price'];				
			}//while loop ends			
		}// if($opt) ends
		//$this->cataloguevendor = $out;
		return $out;
	}
	
	//get item details for a given itemid
	public function get_item_detail($itemid)
	{
		global $dbc;
		$out = NULL;
		$q= "SELECT * FROM items WHERE itemId = '$itemid' LIMIT 1";
		list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
		if($opt)
		{
			$out = mysqli_fetch_assoc($rs);
		}// if($opt) ends
		return $out;
	}
	
	//get item details for a given itemid
	public function is_online_purchase_allowed($itemid)
	{
		$in = get_item_detail($itemid);
		$opa = false;
		if($in)
		{
			if($in['allow_purchase'] == 1 || $in['allow_purchase'] == 2)
				$opa = true;
		}
		else
			msgshow('Sorry, no such item found <b>'.$itemid.'</b> '.__FUNCTION__);
		return $opa;
	}

	// This function will display the error or success messages
	public function msgshow($msg, $type='bad')
	{
		if($type == 'good')
			echo'<span class="successmsg">'.$msg.'</span>';
		else
			echo'<span class="warn">'.$msg.'</span>';
	}
	
	// getting the items in cart
	function getItemInCart($sesid)
	{
		global $dbc;
		list($opt, $rs) = run_query($dbc, $q="SELECT * FROM baskets WHERE  basketSession = '$sesid'", $mode='multi', $msg='');
		if($opt) return ($rs); else return NULL;
	}
	
	// check item in cart
	function checkItemInCart($itemid, $sesid)
	{
		global $dbc;
		list($opt, $rs) = run_query($dbc, $q="SELECT * FROM baskets WHERE  basketSession = '$sesid' AND productID = '$itemid'  LIMIT 1", $mode='single', $msg='');
		if($opt) return true; else return false;
	}
	
	// to update the items in the cart
	function updatecart($itemid, $qty, $sesid)
	{
		global $dbc;
		if($this->checkItemInCart($itemid, $sesid))
		{
			if($qty == 0 || $qty == '' || $qty<0) 
			{
				mysqli_query($dbc, "DELETE  FROM baskets WHERE productID = $itemid AND basketSession = '$sesid'") OR die('Could not update the cart');
				return true;
			}
			else
			{
				mysqli_query($dbc, "UPDATE baskets SET addQTY = $qty WHERE productID = $itemid AND basketSession = '$sesid'") OR die('Could not update the cart');
				return true;
			}
		}
		else
		{
			$itemdetail = $this->get_item_detail($itemid);	 // getting the item best purchase QTY
			if($itemdetail) $productPrice = $itemdetail['sale_price']; else return false;
			mysqli_query($dbc, "INSERT INTO baskets (productID, productPrice, basketSession, addQTY) VALUES ('$itemid', '$productPrice', '$sesid', '$qty')") OR die('Could not insert in  the cart');
				return true;
		}
	}
	
	// to display the cart
	function displaycart($sesid)
	{
		global $dbc;;
		$rs = $this->getItemInCart($sesid);
		if(!$rs) return NULL; // if no items available then we can not display the cart
		$cartitem = NULL;
		while($row = mysqli_fetch_assoc($rs))
		{
			$itemId = $row['productID'];
			$cartitem[$itemId]['itemId'] = $itemId; // storing the item id
			$icart = $this->get_item_detail($itemId);
			//$cartitem[$itemId]['onlinepurchase'] = is_online_purchase_allowed($itemId); // checking if item is allowed for sale online
			$cartitem[$itemId]['salecode'] = $icart['salecode']; // the s code of the item
			$cartitem[$itemId]['purchasecode'] = $icart['purchasecode']; // the p code of the item
			$cartitem[$itemId]['image'] = $icart['filename']; // the item big image
			$cartitem[$itemId]['saleprice'] = (int)$icart['sale_price']; 
			
			$file = $icart['filename'];
		    $extension = substr($file, strrpos($file, '.') + 1);
		    $extension = strtolower($extension);
		    $justimagename = rtrim($file,'.'.$extension);
		    $filename = $justimagename.'_thumb.'.$extension;
			
			$cartitem[$itemId]['thumbnail'] = $filename; // the item thumbnail
			$cartitem[$itemId]['qty'] = (int)$row['addQTY']; // the quantity purchased by the user
			$cartitem[$itemId]['total'] = $cartitem[$itemId]['saleprice']*$row['addQTY'];
		}
		return $cartitem;
	}
	
	// to display the cart
	function cart_preview($sesid)
	{
		global $dbc;
		$rs = $this->getItemInCart($sesid);
		if(!$rs) return NULL; // if no items available then we can not display the cart
		$cartitem = NULL;
		while($row = mysqli_fetch_assoc($rs))
		{
			$itemId = $row['productID'];
			$cartitem[$itemId]['itemId'] = $itemId; // storing the item id
			$icart = $this->get_item_detail($itemId);
			$cartitem[$itemId]['saleprice'] = (int)$icart['sale_price']; 
			$cartitem['qty'][] = (int)$row['addQTY'];  // the quantity purchased by the user
			$cartitem['totalvalue'][] = $cartitem[$itemId]['saleprice']*$row['addQTY'];
		}
		$cartitem['tqty'] = array_sum($cartitem['qty']);
		$cartitem['ctotalvalue'] = array_sum($cartitem['totalvalue']);
		$cartitem['frontcart'] = 'Your Cart : <a href="index.php?option=mycart">'.$cartitem['tqty'].' item(s) INR '.number_format($cartitem['ctotalvalue']).'</a>';
		return $cartitem;
	}
	
	//this function will store the items in basket as the customer online order giving him detail of his/her order data.
	public function store_items_permanently($custid)
	{
		global $dbc;
		$out = array('status'=>false, 'msg'=>'');
		$orderitem = $this->displaycart($custid);
		//if user shopping basket is empty return the user back
		if(empty($orderitem)) {$out['msg'] = 'Sorry, no items in your shopping cart found.'; return $out;}
		$r1 = mysqli_query($dbc, "INSERT INTO online_order (ooId, custId, rosId, remark, order_date) VALUES (NULL, '$custid', '1', 'remark', NOW())");
		if(!$r1) 
		{
			$out['msg'] = 'Sorry your order can not be processed, some error occured';
			return $out;
		}
		else
		{
			$ooId = mysqli_insert_id($dbc);
			$str = '';
			$total = 0;
			foreach($orderitem as $key=>$value)
			{
				$str .= "($ooId, $key, {$value['qty']}, {$value['saleprice']}), ";
				$total += $value['total'];
			}
			$str = rtrim($str,', ');
			$r2 = mysqli_query($dbc, "INSERT INTO online_order_item (ooId, itemId, qty, rate) VALUES $str");
			if(!$r2) {$out['msg'] = 'Sorry, order items could not be saved.'; return $out;}
			$r3 = mysqli_query($dbc, "UPDATE online_order SET billamount = $total WHERE ooId = $ooId");
			if(!$r3) {$out['msg'] = 'Sorry, your bill amount updation failed.'; return $out;}
			$out['msg'] = "Your order successfully received, <br> your Order No is : <strong>$ooId</strong>";
			$out['status'] = true;
			$out['orderid'] = $ooId;
			// making the customer shopping cart empty
			mysqli_query($dbc, $q="DELETE FROM baskets WHERE basketSession = '$custid'");
			return $out;
		}
	}
	
	public function oo_filter($filter,  $records = '')
	{
		$filterstr = '';
		// if the filter condition are array
		if(is_array($filter) && !empty($filter))
			$filterstr = "WHERE ".implode(' AND ',$filter);
		elseif(!empty($filter))
			$filterstr = "WHERE $filter";
		if(empty($filterstr) && !empty($records))
			$filterstr = " LIMIT $records";
		elseif(!empty($filterstr) && !empty($records))
			$filterstr .= " LIMIT $records";
		return $filterstr;
	}
	
	// This function will fetch data about the online customer orders
	// filter = it can be an array or string to sort out particular orders
	// $records = a number or range which indicate how many records to show.
	public function get_oo_list($filter='',  $records = '')
	{
		global $dbc;
		$out = array();		
		// if user has send some filter use them.
		$filterstr = '';
		if(!empty($filter) || !empty($records))
			$filterstr = $this->oo_filter($filter, $records);
		
		$q = "SELECT online_order.*, rosname, DATE_FORMAT(order_date, '%d/%b/%Y <br/> %r') AS forder_date, custname, email, loginid FROM online_order INNER JOIN customers USING(custId) INNER JOIN ref_order_status USING(rosId) $filterstr";
		list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
		if(!$opt) return $out; // if no order placed send blank array
		while($row = mysqli_fetch_assoc($rs))
		{
			$id = $row['ooId'];
			$out[$id]['ooId'] = $id; // storing the item id
			$out[$id]['totalamount'] = $row['billamount'];
			$out[$id]['taxamount'] = $row['taxamount'];
			$out[$id]['discount'] = $row['discount'];
			$out[$id]['remark'] = $row['remark'];
			$out[$id]['order_date'] = $row['forder_date'];
			$out[$id]['orderstatus'] = $row['rosname'];
			$out[$id]['rosId'] = $row['rosId'];
			
			//setting the customer detail
			$out[$id]['custId'] = $row['custId'];
			$out[$id]['custname'] = $row['custname'];
			$out[$id]['email'] = $row['email'];
			$out[$id]['loginid'] = $row['loginid'];
		}
		return $out;
	}
	
	//This function will list the item order for a giver order id
	public function get_oo_item($filter='',  $records = '')
	{
		global $dbc;
		$out = array();		
		// if user has send some filter use them.
		$filterstr = '';
		if(!empty($filter) || !empty($records))
			$filterstr = $this->oo_filter($filter, $records);
		
		$q = "SELECT online_order_item.*, salecode, purchasecode, itemcode, filename FROM online_order_item INNER JOIN items USING(itemId) INNER JOIN online_order USING(ooId) $filterstr";
		list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
		if(!$opt) return $out; // if no order placed send blank array
		while($row = mysqli_fetch_assoc($rs))
		{
			$id = $row['itemId'];
			$ooId = $row['ooId'];
			$out[$ooId][$id]['itemId'] = $id; // storing the item id
			$out[$ooId][$id]['qty'] = $row['qty'];
			$out[$ooId][$id]['saleprice'] = (int)$row['rate'];
			$out[$ooId][$id]['image'] = $row['filename'];
			$out[$ooId][$id]['purchasecode'] = $row['purchasecode'];
			$out[$ooId][$id]['salecode'] = $row['salecode'];			
			$out[$ooId][$id]['itemcode'] = $row['itemcode'];
			$out[$ooId][$id]['ooId'] = $row['ooId'];
			$out[$ooId][$id]['total'] = $row['qty']*$row['rate'];
		}
		return $out;
	}
	
	//This function will list the oline challan item sent detail for a given challan id
	public function get_oo_challan_item($filter='',  $records = '')
	{
		global $dbc;
		$out = array();		
		// if user has send some filter use them.
		$filterstr = '';
		if(!empty($filter) || !empty($records))
			$filterstr = $this->oo_filter($filter, $records);
		
		$q = "SELECT oo_challan_item.*, salecode, purchasecode, itemcode, filename FROM oo_challan_item INNER JOIN items USING(itemId) INNER JOIN oo_challan USING(ooId) $filterstr";
		list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
		if(!$opt) return $out; // if no order placed send blank array
		while($row = mysqli_fetch_assoc($rs))
		{
			$id = $row['itemId'];
			$ooId = $row['oocId'];
			$out[$ooId][$id]['itemId'] = $id; // storing the item id
			$out[$ooId][$id]['qty'] = $row['qty'];
			$out[$ooId][$id]['saleprice'] = (int)$row['rate'];
			$out[$ooId][$id]['image'] = $row['filename'];
			$out[$ooId][$id]['purchasecode'] = $row['purchasecode'];
			$out[$ooId][$id]['salecode'] = $row['salecode'];			
			$out[$ooId][$id]['itemcode'] = $row['itemcode'];
			$out[$ooId][$id]['oocId'] = $row['oocId'];
			$out[$ooId][$id]['total'] = $row['qty']*$row['rate'];
		}
		return $out;
	}
	
	// This function will tell us the amount of qty sent against a particluar order of a particular item
	public function oo_itemSent_orderWise($itemId='', $ooId='')
	{
		global $dbc;
		$out = 0;
		list($opt, $rs) = run_query($dbc, $q="SELECT SUM(qty) AS qty FROM oo_challan_item WHERE item_id = '$itemId' AND orderId = '$ooId'", $mode='single', $msg='');
		$q;
		if($opt) 
			$out = (int) $rs['qty'];
		return $out;
	}
	
	//This function will tell us the balance qty left for a given item against a given order
	public function oo_itemBalance_orderWise($itemId='', $ooId='', $orderqty = '')
	{
		global $dbc;
		$out = 0;
		// Getting the qty of the item sent for a given itemid and given orderId
		$sent = $this->oo_itemSent_orderWise($itemId, $ooId);
		//if the order qty has been sent as parameter then no need to query the database for its value
		if(!empty($orderqty))
			$userOrderQty = $orderqty;
		else
		{
			$q="SELECT qty FROM online_order_item WHERE itemId = '$itemId' AND ooId = '$ooId'";
			list($opt, $rs) = run_query($dbc, $q, $mode='single', $msg='');
			if($opt) 
				$userOrderQty = (int) $rs['qty'];
		}
		//Balance left
		$out = $userOrderQty - $sent;		
		return $out;
	}
	
	//This function will check whether an online order is complete or not
	public function is_oo_sent_complete($orderId, $showbalance = false)
	{
		global $dbc;
		$ocomplete = true;
		$out = array('ocomplete'=>$ocomplete, 'ordered'=>NULL, 'sent'=>NULL, 'balance'=>NULL);
		//fetching the online order quantity details
		list($opt, $rs) = run_query($dbc, $q="SELECT itemId,qty FROM online_order_item WHERE ooId = '$orderId' ORDER BY itemId ASC", $mode='multi', $msg='');
		
		// If there is no order found
		if(!$opt)
			 myquit($option = 'Sorry, no such Order found', $qtype='w');
		
		// If ther is any order found
		if($opt)
		{
			list($opts, $rss) = run_query($dbc, $q="SELECT itemId, SUM(qty) AS qty FROM oo_challan_item WHERE orderId = '$orderId' GROUP BY itemId ORDER BY itemId ASC", $mode='multi', $msg='');
			// to hold the ordered item quantity
			$orderqty = array(); 				
			while($row = mysqli_fetch_assoc($rs))
				$orderqty[$row['itemId']] = $row['qty'];	
			//if nothing is sent	
			if(!$opts)
			{
				$ocomplete =  false;
				if($showbalance)
				{
					$sentqty = array();
					$balance = array();
					foreach($orderqty as $key=>$value)
						$sentqty[$key] = $balance[$key] = $value;
					$out['ordered'] =  	$orderqty;
					$out['sent'] =  	$sentqty;
					$out['balance'] =  	$balance;
					$out['ocomplete'] =  $ocomplete;
				}
			}
			else
			{
				
				// to hold the ordered sent quantity	
				$sentqty = array(); 			
				while($rows = mysqli_fetch_assoc($rss))
					$sentqty[$rows['itemId']] = $rows['qty'];	
				//calculation the balance
				$balance = array();
				foreach($orderqty as $key=>$value)
					$balance[$key] = isset($sentqty[$key]) ? ($orderqty[$key]-$sentqty[$key]):$orderqty[$key];
				$totleft = 0;
				foreach($balance as $key=>$value)
				{
					//we have ignored the qty that ar (-)ve as it will happen only when excess qty of good is sent
					if($value > 0) $totleft += $value;
				}				
				if($totleft > 0) $ocomplete = false;
				if($showbalance)
				{
					$out['ordered'] =  	$orderqty;
					$out['sent'] =  	$sentqty;
					$out['balance'] =  	$balance;
					$out['ocomplete'] =  $ocomplete;
				}
			}
		}
		if($ocomplete)
			mysqli_query($dbc, "UPDATE online_order SET rosId = 2 WHERE ooId='$orderId' LIMIT 1");
		//else
		//	mysqli_query($dbc, "UPDATE online_order SET rosId = 1 WHERE ooId='$orderId' LIMIT 1");
			
		//if user is asking for an array of QTY then send the array else just sent the result only
		if($showbalance) return $out; else return $ocomplete;
	}
	
	public static function is_order_modifiable($orderid)
	{
		global $dbc;
		$tflag = false;
		$oflag = false;
		list($opt, $rs) = run_query($dbc, $q="SELECT rosId FROM saleorder WHERE soId = '$orderid' LIMIT 1", $mode='single', $msg='');	
		
		// if the order is new one or if its resheduled then order flag is true
		if($rs['rosId'] == 1 || $rs['rosId'] == 7) $oflag = true;
		// checking whether the time boundation allow order modification or not
		list($opt3, $rs3) = run_query($dbc, $q = "SELECT HOUR(TIMEDIFF(startperiod, TIME_FORMAT(NOW(),'%T'))) AS baltime, DATEDIFF(delivery_date_customer,NOW()) AS baldays, (SELECT stvalue FROM settings WHERE stId = 2) AS timeset FROM `saleorder` INNER JOIN delivery_slot USING (dsId) WHERE soId = $orderid", $mode='single', $msg = '');	
		if($opt3)
		{
			  if($rs3['baldays'] > 0)
				  $tflag = true;
			  elseif($rs3['baltime'] > $rs3['timeset'])
				  $tflag = true;
		}
		// if both the flags are ok then user can modify the orders
		if($tflag && $oflag)
			return true;
		else
			return false;
	}
}
//$fes1 = new front_end_sale(NULL);
//print_r ($fes1->store_items_permanently(9));
?>