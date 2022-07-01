<?php
class sale
{
	private $id = NULL;
	private $id_detail = NULL;
	
	function __construct($id)
	{
		$this->id = $id;
	}
	// This function will prepare sale detail for a given custId in a given date range
	public function sale_by_date($custId,$start,$end)
	{
		global $dbc;
		$out = array();
		$start = get_mysql_date($start,'/',$time = false, $mysqlsearch = true);
		$end = get_mysql_date($end,'/',$time = false, $mysqlsearch = true);
		$q= "SELECT * FROM sale WHERE DATE_FORMAT(saledate,'".MYSQL_DATE_SEARCH."') >= '$start' AND DATE_FORMAT(saledate,'".MYSQL_DATE_SEARCH."') <= '$end' AND custId = $custId";
		list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
		if($opt)
		{
			while($row = mysqli_fetch_assoc($rs))
			{
				$q = "SELECT sum(qty) AS totqty FROM sale_item WHERE saleId = $row[saleId]";
				list($opt1, $rs1) = run_query($dbc, $q, $mode='single', $msg='');
				$out[$row['saleId']]['date'] = $row['saledate'];
				$out[$row['saleId']]['qty'] = 0;
				if($opt1)
					$out[$row['saleId']]['qty'] = $rs1['totqty'];
			}//while loop ends
		}// if($opt) ends
		return $out;
	}
	// This will give the detail of the sale for a given saleid
	public function sale_by_id($saleid)
	{
		global $dbc;
		$out = array();
		$q= "SELECT * FROM sale WHERE saleId = $saleid";
		list($opt, $rs) = run_query($dbc, $q, $mode='single', $msg='');
		if($opt)
		{
			$q = "SELECT rate, itemId, salecode, qty, filename FROM sale_item INNER JOIN items USING(itemId) WHERE saleId = $rs[saleId]";
			list($opt1, $rs1) = run_query($dbc, $q, $mode='multi', $msg='');
			if($opt1)
			{
				while($row = mysqli_fetch_assoc($rs1))
				{
					$out[$row['itemId']]['salecode'] = $row['salecode'];
					$out[$row['itemId']]['qty'] = $row['qty'];
					$out[$row['itemId']]['filename'] = $row['filename'];
					$out[$row['itemId']]['rate'] = $row['rate'];
				}//while loop ends
			}// if($opt1) ends
		}// if($opt) ends
		return $out;
	}
	
	public function sale_by_vendor($custId,$start,$end)
	{
		global $dbc;
		$out = array();
		$start = get_mysql_date($start,'/',$time = false, $mysqlsearch = true);
		$end = get_mysql_date($end,'/',$time = false, $mysqlsearch = true);
		$q= "SELECT saleId FROM sale WHERE DATE_FORMAT(saledate,'".MYSQL_DATE_SEARCH."') >= '$start' AND DATE_FORMAT(saledate,'".MYSQL_DATE_SEARCH."') <= '$end' AND custId = $custId";
		$q1 = "SELECT DISTINCT itemId FROM sale_item WHERE saleId IN ($q)";
		$q2 = "SELECT code, isvId FROM item_source_vendor INNER JOIN items USING(isvId) WHERE itemId IN ($q1)";
		list($opt, $rs) = run_query($dbc, $q2, $mode='multi', $msg='');
		if($opt)
		{
			while($row = mysqli_fetch_assoc($rs))
			{
				$out[$row['isvId']]['vendor'] = $row['code'];
			}//while loop ends
		}// if($opt) ends
		return $out;
	}
	
	public function sale_by_vendorid($custId,$start,$end,$isvId)
	{
		global $dbc;
		$out = array();
		$start = get_mysql_date($start,'/',$time = false, $mysqlsearch = true);
		$end = get_mysql_date($end,'/',$time = false, $mysqlsearch = true);
		$q= "SELECT saleId FROM sale WHERE DATE_FORMAT(saledate,'".MYSQL_DATE_SEARCH."') >= '$start' AND DATE_FORMAT(saledate,'".MYSQL_DATE_SEARCH."') <= '$end' AND custId = $custId";
		$q1 = "SELECT itemId, qty FROM sale_item INNER JOIN items USING(itemId) WHERE saleId IN ($q) AND isvId = '$isvId'";
		//$q2 = "SELECT code, isvId FROM item_source_vendor INNER JOIN items USING(isvId) WHERE itemId IN ($q1)";
		list($opt, $rs) = run_query($dbc, $q1, $mode='multi', $msg='');
		if($opt)
		{
			while($row = mysqli_fetch_assoc($rs))
			{
				if(isset($out[$row['itemId']]['qty']))
					$out[$row['itemId']]['qty'] += $row['qty'];
				else
					$out[$row['itemId']]['qty'] = $row['qty'];
			}//while loop ends
			$str = '';
			foreach($out as $key=>$value)
				$str .= "$key, ";
			$str = rtrim($str, ', ');
			$q = "SELECT itemId, salecode, filename FROM items WHERE itemId IN ($str)";
			list($opt1, $rs1) = run_query($dbc, $q, $mode='multi', $msg='');
			if($opt1)
			{
				while($row1 = mysqli_fetch_assoc($rs1))
				{
					$out[$row1['itemId']]['salecode'] = $row1['salecode'];
					$out[$row1['itemId']]['filename'] = $row1['filename'];
				}// while loop ends
				
			}// if($opt1) ends
				
		}// if($opt) ends
		return $out;
	}
	
	public function sale_by_itemid($custId,$start,$end,$itemId)
	{
		global $dbc;
		$out = array();
		$start = get_mysql_date($start,'/',$time = false, $mysqlsearch = true);
		$end = get_mysql_date($end,'/',$time = false, $mysqlsearch = true);
		$q= "SELECT saleId FROM sale WHERE DATE_FORMAT(saledate,'".MYSQL_DATE_SEARCH."') >= '$start' AND DATE_FORMAT(saledate,'".MYSQL_DATE_SEARCH."') <= '$end' AND custId = $custId";
		$q1 = "SELECT saleId, saledate, itemId, qty FROM sale INNER JOIN sale_item USING(saleId) WHERE sale_item.saleId IN ($q) AND itemId = $itemId";
		list($opt, $rs) = run_query($dbc, $q1, $mode='multi', $msg='');
		if($opt)
		{
			while($row = mysqli_fetch_assoc($rs))
			{
				$out[$row['saleId']]['date'] = $row['qty'];
				$out[$row['saleId']]['qty'] = $row['qty'];
			}//while loop ends
				
		}// if($opt) ends
		return $out;
	}
	
	public function sale_by_total($custId,$start,$end)
	{
		global $dbc;
		$out = array();
		$start = get_mysql_date($start,'/',$time = false, $mysqlsearch = true);
		$end = get_mysql_date($end,'/',$time = false, $mysqlsearch = true);
		$q= "SELECT saleId FROM sale WHERE DATE_FORMAT(saledate,'".MYSQL_DATE_SEARCH."') >= '$start' AND DATE_FORMAT(saledate,'".MYSQL_DATE_SEARCH."') <= '$end' AND custId = $custId";
		$q1 = "SELECT sum(qty) as fqty, itemId FROM sale_item WHERE saleId IN ($q) GROUP BY itemId";
		list($opt, $rs) = run_query($dbc, $q1, $mode='multi', $msg='');
		if($opt)
		{
			while($row = mysqli_fetch_assoc($rs))
			{
				$out[$row['itemId']]['qty'] = $row['fqty'];
			}//while loop ends
				
		}// if($opt) ends
		return $out;
	}
	
	// getting the details of item By id
	public function itembyId($itemId)
	{
		global $dbc;
		$q = "SELECT * FROM items WHERE itemId = $itemId";
		list($opt, $rs) = run_query($dbc, $q, $mode='single', $msg='');
		if($opt) return $rs; else return NULL;
	}
	
}
?>