<?php 
class tax extends myfilter
{
	public $id = NULL;
	public $id_detail = NULL;
	
	public function __construct($id = NULL)
	{
		parent::__construct();
		$this->id = $id;
	}	
	
	public function get_tax_se_data()
	{
		//$_POST['taxtype'] = 1; $_POST['taxname'] = 'Revenue Tax';  $_POST['taxvalue'] = '6'; 
		$d1 = array('taxtype'=>$_POST['taxtype'], 'taxname'=>ucwords(strtolower($_POST['taxname'])), 'taxvalue'=>$_POST['taxvalue'], 'uid'=>$_SESSION[SESS.'id'],'sesId'=>$_SESSION[SESS.'sess']['ses_id']);
		$d1['myreason'] = 'Please fill all the required information';
		$d1['what'] = 'Tax'; //whether to do history log or not
		return array(true,$d1);	
	}
	
	public function tax_save()
	{ 
		global $dbc;
		$out= array('status'=>'false','myreason'=>'');
		list($status,$d1)= $this ->get_tax_se_data();  
		if(!$status) return array('status'=>false, 'myreason'=>$d1['myreason']);		
		mysqli_query($dbc, "START TRANSACTION");
		$q = "INSERT INTO `tax` (`taxId`, `taxname`, `taxtype`, `taxvalue`, `locked`) VALUES (NULL, '$d1[taxname]', '$d1[taxtype]', '$d1[taxvalue]', '0')";
		$r = mysqli_query($dbc, $q);
		if(!$r){mysqli_rollback($dbc); return array ('status'=>false, 'myreasdon'=>'tax Table error') ;} 
		$rId = mysqli_insert_id($dbc);	
		mysqli_commit($dbc);
		//Logging the history
		history_log($dbc, 'Add', 'tax <b>'.$d1['taxname'].'</b> with With RefCode : '.$rId, $d1['what']);
		//Final success 
		return array ('status'=>true,'myreason'=>$d1['what'].' successfully Saved');
	}
	
    public function tax_edit($id)
	{
		global $dbc;
		$out = array('status'=>'false','myreason'=>'');
		list($status,$d1)=$this->get_tax_se_data();
		if(!$status) return array ('staus'=>false,'myreason'=>$d1['myreason']);
		//Checking whether the original data was modified or not
		$originaldata = $this->get_tax_list("taxId = $id");
		$originaldata = $originaldata[$id];
		$modifieddata = $this->get_modified_data($originaldata, $d1);
		if(empty($modifieddata)) return array('status'=>false, 'myreason'=>'Please do <strong>atleast 1 change</strong> to update');
		//start the transaction
		mysqli_query($dbc, "START TRANSACTION");
		// query to update 
		$q="UPDATE tax SET taxname = '$d1[taxname]', taxtype = '$d1[taxtype]', taxvalue = '$d1[taxvalue]' WHERE taxId='$id'";
		$r=mysqli_query($dbc, $q);
		if(!$r){mysqli_rollback($dbc); return array ('status'=>false, 'myreason'=>'tax Table error') ;} 
		$rId = mysqli_insert_id($dbc);	
		mysqli_commit($dbc);
		//Saving the user modification history
		$hid = history_log($dbc, 'Edit', 'tax <strong>'.$d1['taxname'].'</strong> With RefCode :'.$id);
		$this->save_log($hid, $modifieddata, $d1['what']);
		return array ('status'=>true,'myreason'=>$d1['what'].' successfully Updated');
	}
	
	public function get_tax_list($filter='', $records='', $orderby='')
	{
		global $dbc;
                $filterstr=$this->oo_filter($filter, $records, $orderby);
		$out = array();
                $date = date('Y-m-d');
                $match_date = date('Ymd');
                // for finding current event
                $q = "SELECT * FROM event WHERE DATE_FORMAT(event_date,'%Y%m%d') = '$match_date'";
		
		list($opt,$rs)= run_query($dbc,$q,$mode='multi',$msg='');
		if(!$opt) return $out; 
		while($row = mysqli_fetch_assoc($rs)){
			$id = $row['event_id'];
			$out[$id]['curent_event'] = $row; // storing the item id
		}// while($row = mysqli_fetch_assoc($rs)){ ends
                
                 // for finding endend event
                $q = "SELECT * FROM event WHERE DATE_FORMAT(event_date,'%Y%m%d') > '$match_date'";
		list($opt,$rs)= run_query($dbc,$q,$mode='multi',$msg='');
		if(!$opt) return $out; 
		while($row = mysqli_fetch_assoc($rs)){
			$id = $row['event_id'];
			$out[$id]['coming_event'] = $row; // storing the item id
		}// while($row = mysqli_fetch_assoc($rs)){ ends
                // for finding comoing event
                $q = "SELECT * FROM event WHERE DATE_FORMAT(event_date,'%Y%m%d') < '$match_date'";
		list($opt,$rs)= run_query($dbc,$q,$mode='multi',$msg='');
		if(!$opt) return $out; 
		while($row = mysqli_fetch_assoc($rs)){
			$id = $row['event_id'];
			$out[$id]['ended_event'] = $row; // storing the item id
		}// while($row = mysqli_fetch_assoc($rs)){ ends
		return $out;	
	}
	public function get_vat_list($filter='', $records='', $orderby='')
	{
		global $dbc;
		$out = array();
		$filterstr=$this->oo_filter($filter, $records, $orderby);
		$q="SELECT * FROM vat_breakup $filterstr";
		list($opt,$rs)= run_query($dbc,$q,$mode='multi',$msg='');
		if(!$opt) return $out; 
		while($row = mysqli_fetch_assoc($rs)){
			$id = $row['vbId'];
			$out[$id] = $row; // storing the item id
		}// while($row = mysqli_fetch_assoc($rs)){ ends
		return $out;	
	}
	public function get_vat_list_detail($filter='', $records='', $orderby='')
	{
		global $dbc;
		$out = array();
		$filterstr=$this->oo_filter($filter, $records, $orderby);
		$q="SELECT * FROM vat $filterstr";
		list($opt,$rs)= run_query($dbc,$q,$mode='multi',$msg='');
		if(!$opt) return $out; 
		while($row = mysqli_fetch_assoc($rs)){
			$id = $row['vatId'];
			$out[$id] = $row; // storing the item id
			$out[$id]['vat_details'] = $this->get_my_reference_array_direct("SELECT * FROM vat_breakup  WHERE vatId = $id ORDER BY sortorder ASC", 'vbId'); 
		}// while($row = mysqli_fetch_assoc($rs)){ ends
		return $out;	
	}
	public function get_vat_name($filter='', $records='', $orderby='')
	{
		global $dbc;
		$out = array();
		$filterstr=$this->oo_filter($filter, $records, $orderby);
		$q="SELECT * FROM vat $filterstr";
		list($opt,$rs)= run_query($dbc,$q,$mode='single',$msg='');
		if(!$opt) return $out; 
		return $rs['vatname'];	
	}
	 public function vat_edit()
	{
		global $dbc;
		$out = array('status'=>'false','myreason'=>'');
		$vbId = $_POST['vbId'];
		$taxname = $_POST['taxname'];
		$taxvalue = $_POST['taxvalue'];
		if(!empty($vbId))
		{
			$i = 1;
			foreach($vbId as $key=>$value)
			{
				$q = "UPDATE vat_breakup SET taxname = '{$taxname[$key]}', taxvalue = '{$taxvalue[$key]}' WHERE vbId = '$value'";
				$r = mysqli_query($dbc, $q);
				$i++;
			}
		}
		//start the transaction
		if($i > 1) return array ('status'=>TRUE, 'myreason'=>'Vat Table Succesfully Updated') ; 
		mysqli_commit($dbc);
		//Saving the user modification history
	}
}
?>