<?php
// This class will handle all the task related to packing slip in and bill of packing slips
class history
{
	public function __construct()
	{
		
	}
	
	//This function will give a clean identifier having numbers and alphabets only
	public function _prepare_url_text($string, $replacement = '-')
	{
		$not_accept = '#[^-a-zA-Z0-9_ ]#';	
		$string = preg_replace($not_accept,'', $string);
		
		$string = trim($string);
		$string = preg_replace('#[-_ ]+#', $replacement, $string);		
		return $string;
	}
	
	//This function will save the log of exact value wise change details
	public function save_log($hid, $modifieddata, $identifier='')
	{
		global $dbc;
		if(is_null($hid)) return;
		if(!empty($modifieddata)){
			$serialize_basket_content = base64_encode(serialize($modifieddata));
			//unserialize(base64_decode($rs['baskdata']));
			mysqli_query($dbc, "UPDATE history_log SET history_data = '$serialize_basket_content', identifier = '$identifier' WHERE hid = $hid LIMIT 1");
		}//if(!empty($modifieddata)){ ends
	}
	
	public function get_modified_data($originaldata, $newdata)
	{
		global $dbc;
		$modifieddata = array();
		foreach($newdata as $key=>$value){
			if(isset($originaldata[$key]) && isset($newdata[$key])){
				if($originaldata[$key] != $newdata[$key]){
					$modifieddata[$key]['orignalvalue'] = $originaldata[$key];
					$modifieddata[$key]['modifiedvalue'] = $newdata[$key];
				}// if($originaldata[$key] != $newdata[$key]){ edns
			}// if(isset($originaldata[$key]) && isset($newdata[$key])){ ends
		}// foreach($orignaldata as $key=>$value){ ends
		return $modifieddata;
	}
	
	public function get_history_list($filter='',  $records = '', $orderby='')
	{
		global $dbc;
		$out = array();		
		// if user has send some filter use them.
		$filterstr = $this->hoo_filter($filter, $records, $orderby);
		
		$q = "SELECT * FROM history_log $filterstr ";
		list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
		if(!$opt) return $out; // if no order placed send blank array
		// Getting the user details
		$users = $this->get_my_reference_array('admin', 'id', 'uname');
		while($row = mysqli_fetch_assoc($rs))
		{
			$id = $row['hid'];
			$out[$id] = $row; // storing the item id
			$out[$id]['user_id_val'] = isset($users[$row['user_id']]) ? $users[$row['user_id']] : '';
		}
		return $out;
	} 
	
	// This function will build the reference array for single column
	public function get_my_reference_array($tablename, $primarykey, $column, $orderby= '', $outtype = 'single')
	{
		global $dbc;
		$out = array();
		list($opt, $rs) = run_query($dbc, "SELECT * FROM $tablename $orderby", 'multi');
		if(!$opt) return $out;
		while($row = mysqli_fetch_assoc($rs)){
			$id = $row[$primarykey];
			if($outtype == 'multi')
				$out[$id][$column] = $row[$column];
			else
				$out[$id] = $row[$column];
		}
		return $out;
	}
	// This function will build the reference array for multi column
	public function get_my_reference_array_direct($q, $primarykey)
	{
		global $dbc;
		$out = array();
		list($opt, $rs) = run_query($dbc, $q, 'multi');
		if(!$opt) return $out;
		while($row = mysqli_fetch_assoc($rs)){
			$id = $row[$primarykey];
			$out[$id] = $row;
		}
		return $out;
	}
	
	public function hoo_filter($filter,  $records = '', $orderby='')
	{
		$filterstr = '';
		// if the filter condition are array
		if(is_array($filter) && !empty($filter))
			$filterstr = "WHERE ".implode(' AND ',$filter);
		elseif(!empty($filter))
			$filterstr = "WHERE $filter";
			
		if(!empty($orderby)) $filterstr .= " $orderby ";
		
		if(empty($filterstr) && !empty($records))
			$filterstr = " LIMIT $records";
		elseif(!empty($filterstr) && !empty($records))
			$filterstr .= " LIMIT $records";
		return $filterstr;
	}
	
	// This function will build the reference array for multi column
	public function get_refno($q, $primarykey='num')
	{
		global $dbc;
		$out = NULL;
		list($opt, $rs) = run_query($dbc, $q, 'single');
		if($opt) $out = (int)$rs[$primarykey]+1;
		return $out;
	}
	
	// This function will build the reference array for multi column
	public function get_dataByName($q,$totfield=1)
	{
		global $dbc;
		$out = '';
		$r = mysqli_query($dbc, $q);
		if($r && mysqli_num_rows($r) > 0){
			$rs = $totfield == 1 ? mysqli_fetch_row($r) : mysqli_fetch_assoc($r);	
			$out = $totfield == 1 ? $rs[0] : $rs;
		}
		return $out;
	}
	
	######################################## Item Category Metat Data Starts here ############################################	
	public function get_item_category_meta_data($saver='')
	{  
		return array('icname'=>'Item Category', 'istatus'=>'Status');
	}
	######################################## Item Category Metat Data ENDS here ############################################
	
	######################################## Item Unit Metat Data Starts here ############################################	
	public function get_item_unit_meta_data($saver='')
	{  
		return array('utname'=>'Item Unit');
	}
	######################################## Item Unit Metat Data ENDS here ############################################
}// class end here

?>
