<?php
// This class will handle all the task related to packing slip in and bill of packing slips
class schedule extends myfilter
{
	public $poid = NULL;
	
	public function __construct()
	{
		parent::__construct();
	}

	######################################## Item start here ######################################################	
	
	public function schedule_edit($itemunique,$sch_date,$boundtype,$cmpId)
	{
		global $dbc;	
		$out = array('status'=>false, 'myreason'=>'');
		mysqli_query($dbc, "START TRANSACTION");
		$inc=0;
		foreach($itemunique as $key=>$value){
			//$woi_key = $rId.'-'.$value;
			$schedule_date = get_mysql_date($sch_date[$key]);
			$item_unique = $itemunique[$key];
			$company = $cmpId[$key];
			$bond = $boundtype[$key];
			if(!empty($schedule_date))
			{
			    $q = "UPDATE work_order_challan_item SET sch_date = '$schedule_date', cmpId = '$company', boundtype = '$bond', sch_lock = '1' WHERE itmuniqId ='$item_unique' LIMIT 1";
				$r = mysqli_query($dbc,$q);
			}
			else
			{
				 $q = "UPDATE work_order_challan_item SET sch_date =NULL, cmpId = '', boundtype = '', sch_lock = '0' WHERE itmuniqId ='$item_unique' LIMIT 1";				 			                 
				 $r = mysqli_query($dbc,$q);
			}
			$inc++;
		}
		if($inc==0){mysqli_rollback($dbc); return array('status'=>false, 'myreason'=>'Challan Order table  error');}
		mysqli_commit($dbc);
		//Saving the user modification history
		//$hid = history_log($dbc, 'Edit', 'item <strong>'.$d1['item_name'].'</strong> With RefCode :'.$id);
		//$this->save_log($hid, $modifieddata, $d1['what']);
		return array('status'=>true, 'myreason'=>'Schedule successfully updated');
	}	
	
	public function get_schedule_list($filter='',  $records = '', $orderby='')
	{
		global $dbc;
		$out = array();		
		// if user has send some filter use them.
		$filterstr = $this->oo_filter($filter, $records, $orderby);
		
		$q = "SELECT *, DATE_FORMAT(wo.created,'%e/%b/%Y <br/> %r') AS fdated, DATE_FORMAT(wo.modified,'%e/%b/%Y <br/> %r') AS flastedit,DATE_FORMAT(wo.challan_date,'%e/%m/%Y') AS challan_date,DATE_FORMAT(wo.challan_receive_date,'%e/%m/%Y') AS challan_receive_date FROM work_order_challan AS wo INNER JOIN work_order USING(woId) INNER JOIN party USING(partyId) INNER JOIN party_type USING(ptId) $filterstr ";
		list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
		if(!$opt) return $out; // if no order placed send blank array
		while($row = mysqli_fetch_assoc($rs))
		{
			$id = $row['wocId'];
			$out[$id] = $row; // storing the item id
			$out[$id]['schedule_order_item'] = $this->get_my_reference_array_direct("SELECT *, CONCAT_WS('-',item_name,utname) AS item_name,DATE_FORMAT(sch_date,'%e/%b/%Y') AS sch_date FROM work_order_challan_item INNER JOIN item USING(itemId) INNER JOIN units USING(utId) WHERE wocId = $id ORDER BY itemId ASC", 'itmuniqId');
			//$out[$id]['istatus_val'] = $GLOBALS['istatus'][$row['istatus']];
		}
		return $out;
	} 
	
	######################################## Item end here ######################################################	
	
	
}// class end here

?>
