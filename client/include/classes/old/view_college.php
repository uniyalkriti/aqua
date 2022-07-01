<?php
// This class will handle all the task related to packing slip in and bill of packing slips
class view_college extends myfilter
{
	public $poid = NULL;
	public $pono = NULL;
	
	public function __construct()
	{
		parent::__construct();
	}
	public function college_detail_using_ajax($filter='',  $records = '', $orderby='')
	{
		global $dbc;
		$out = array();		
		// if user has send some filter use them.
		$filterstr = $this->oo_filter($filter, $records, $orderby);
		$q = "SELECT * FROM course_branch INNER JOIN course_branch_university USING(cbId) INNER JOIN course_branch_university_var USING(cbuId) INNER JOIN course_branch_college USING(cbuvId) INNER JOIN college USING(colgId) INNER JOIN ref_duration USING(rduId) INNER JOIN ref_course_mode USING(rcmId) INNER JOIN course USING(cId) INNER JOIN loc_city_district USING(cityId) $filterstr";
		list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
		if(!$opt) return $out; // if no order placed send blank array
		while($row = mysqli_fetch_assoc($rs))
		{
			$id = $row['colgId'];
			$out[$id]['colgId'] = $id; // storing the item id
			$out[$id]['rarId'] = $row['rarId'];
			$out[$id]['loc_countryId'] = $row['countryId'];
			$out[$id]['stateId'] = $row['stateId'];
			$out[$id]['localityId'] = $row['localityId'];
			$out[$id]['cityId'] = $row['cityId'];
			$out[$id]['colg_name'] = $row['colg_name'];
			$out[$id]['cbname'] = $row['cbname'];
			$out[$id]['cname'] = $row['cname'];
			$out[$id]['rduname'] = $row['rduname'];
			$out[$id]['rcmname'] = $row['rcmname'];
			$out[$id]['city_name'] = $row['city_name'];
			/*$out[$id]['city_name'] = $row['city_name'];
			$out[$id]['statename'] = $row['statename'];
			$out[$id]['countryname'] = $row['countryname'];
			$out[$id]['rarname'] = $row['rarname'];
			$out[$id]['website'] = $row['website'];*/
			$out[$id]['logo'] = $row['logo'];
			$out[$id]['cbcId'] = $row['cbcId'];
		}
		return $out;
	}
	public function get_college_locality_wise($filter='',  $records = '', $orderby='')
	{
		global $dbc;
		$out = array();		
		// if user has send some filter use them.
		$filterstr = $this->oo_filter($filter, $records, $orderby);
		$q = "SELECT * FROM college INNER JOIN loc_locality USING(localityId) INNER JOIN loc_state USING(stateId) INNER JOIN  loc_country USING(loc_countryId) $filterstr";
		list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
		if(!$opt) return $out; // if no order placed send blank array
		while($row = mysqli_fetch_assoc($rs))
		{
			$id = $row['colgId'];
			$out[$id]['colgId'] = $id; 
			$out[$id]['localityname'] = $row['localityname']; 
			$out[$id]['loc_countryId'] = $row['countryId'];
			$out[$id]['stateId'] = $row['stateId'];
			$out[$id]['localityId'] = $row['localityId'];
			$out[$id]['statename'] = $row['statename'];
			$out[$id]['countryname'] = $row['countryname'];
		}
		return $out;
	}
	// this function used to fetch the college details using 
	public function get_college_city_wise($filter='',  $records = '', $orderby='')
	{
		global $dbc;
		$out = array();		
		// if user has send some filter use them.
		$filterstr = $this->oo_filter($filter, $records, $orderby);
		$q = "SELECT * FROM loc_locality INNER JOIN loc_city_district USING(cityId) INNER JOIN loc_state USING(stateId) INNER JOIN  loc_country USING(loc_countryId) $filterstr";
		list($opt, $rs) = run_query($dbc, $q, $mode='multi', $msg='');
		if(!$opt) return $out; // if no order placed send blank array
		while($row = mysqli_fetch_assoc($rs))
		{
			$id = $row['localityId'];
			$out['localityId'] = $id; 
			$out['localityname'] = $row['localityname']; 
			$out['loc_countryId'] = $row['loc_countryId'];
			$out['stateId'] = $row['stateId'];
			$out['localityId'] = $row['localityId'];
			$out['statename'] = $row['statename'];
			$out['countryname'] = $row['countryname'];
			$out['city_name'] = $row['city_name'];
		}
		return $out;
	}
}// class end here
?>
