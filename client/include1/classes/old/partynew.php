<?php
// This class will handle all the task related to packing slip in and bill of packing slips
class partynew extends myfilter
{
	public $poid = NULL;
	
	public function __construct()
	{
		parent::__construct();
	}
	
	######################################## Item Category Starts here ####################################################	
	
	public function get_party_list($filter='',  $records = '', $orderby='')
	{
		global $dbc;
		$out = array();		
		// if user has send some filter use them.
		$filterstr = $this->oo_filter($filter, $records, $orderby);
		
		echo $q = "SELECT *,DATE_FORMAT(created,'%e/%b/%Y') AS fdated,DATE_FORMAT(modified,'%e/%b/%Y') AS flastedit FROM party INNER JOIN party_contact USING(partyId) WHERE partyId= '7' ";
		list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
		if(!$opt) return $out; // if no order placed send blank array
		$party_contact = array();
		while($row = mysqli_fetch_assoc($rs))
		{
			$id = $row['partyId'];
			$id1= $row['pcId'];
			$out[$id] = $row; // storing the item id
			$out[$id1][$id]['name'] = $row['name'];
			$out[$id1][$id]['email'] = $row['email'];
			$out[$id1][$id]['mobile'] = $row['mobile'];
			
		}
		return $out;
	} 
	
	######################################## Item Category Ends here ######################################################	
	
	
	
}// class end here

?>
