<?php
class myfilter extends history
{
	public function __construct()
	{
		parent::__construct();
	}
	
	public function oo_filter($filter,  $records = '', $orderby='')
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
}
?>