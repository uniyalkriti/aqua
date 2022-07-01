<?php 
class equipment extends myfilter
{
	public $id = NULL;
	public $id_detail = NULL;
	
	public function __construct($id = NULL)
	{
		parent::__construct();
		$this->id = $id;
	}	
	
	public function get_equipment_se_data()
	{
		/*$_POST['eqpname'] = $_POST['eqpmake'] = $_POST['certno'] = $_POST['traceability'] =  1;	
		$_POST['due_date'] = '20/01/2014';*/
 
		$d1 = array('eqpname'=>$_POST['eqpname'], 'eqpmake'=>$_POST['eqpmake'], 'certno'=>$_POST['certno'], 'due_date'=>$_POST['due_date'], 'traceability'=>$_POST['traceability'], 'uid'=>$_SESSION[SESS.'id'],'sesId'=>$_SESSION[SESS.'csess']);
		$d1['myreason'] = 'Please fill all the required information';
		$d1['what'] = 'Equipment'; //whether to do history log or not
		return array(true,$d1);	
	}
	
	public function equipment_save()
	{ 
		global $dbc;
		$out= array('status'=>'false','myreason'=>'');
		list($status,$d1)= $this ->get_equipment_se_data();  
		if(!$status) return array('status'=>false, 'myreason'=>$d1['myreason']);		
		mysqli_query($dbc, "START TRANSACTION");
		$due_date = !empty($d1['due_date']) ? get_mysql_date($d1['due_date'], '/', false, false) : '';
		$q = "INSERT INTO `master_equipment` (`tmeId`, `eqpname`, `eqpmake`, `certno`, `due_date`, `traceability`, `created`, `crId`)
				
		 VALUES (NULL, '$d1[eqpname]', '$d1[eqpmake]', '$d1[certno]', '$due_date', '$d1[traceability]', NOW(), '$d1[uid]')";
		 
		$r = mysqli_query($dbc, $q);
		if(!$r){mysqli_rollback($dbc); return array ('status'=>false, 'myreason'=>'Master Equipment Table error') ;} 
		$rId = mysqli_insert_id($dbc);
		mysqli_commit($dbc);
		//Logging the history
		history_log($dbc, 'Add', 'Equipment received <b>'.$d1['certno'].'</b> with With RefCode : '.$rId, $d1['what']);
		//Final success 
		return array ('status'=>true,'myreason'=>$d1['what'].' successfully Saved', 'rId'=>$rId);
	}
	
    public function equipment_edit($id)
	{
		global $dbc;
		$out = array('status'=>'false','myreason'=>'');
		list($status,$d1)=$this->get_equipment_se_data();
		if(!$status) return array ('staus'=>false,'myreason'=>$d1['myreason']);
		//Checking whether the original data was modified or not
		/*$originaldata = $this->get_item_list("itemId = $id");
		$originaldata = $originaldata[$id];
		$modifieddata = $this->get_modified_data($originaldata, $d1);
		if(empty($modifieddata)) return array('status'=>false, 'myreason'=>'Please do <strong>atleast 1 change</strong> to update');*/
		//start the transaction
		mysqli_query($dbc, "START TRANSACTION");
		// query to update 
		$due_date = !empty($d1['due_date']) ? get_mysql_date($d1['due_date'], '/', false, false) : '';
		$q = "UPDATE master_equipment SET eqpname = '$d1[eqpname]', eqpmake = '$d1[eqpmake]', due_date = '$due_date', certno = '$d1[certno]', traceability = '$d1[traceability]', modified = NOW(), mrId = '$d1[uid]' WHERE tmeId='$id'";
		$r=mysqli_query($dbc, $q);
		if(!$r){mysqli_rollback($dbc); return array ('status'=>false, 'myreason'=>'Master Equipment Table error') ;}
		mysqli_commit($dbc);
		//Saving the user modification history
		$hid = history_log($dbc, 'Edit', 'Equipment received <strong>'.$d1['certno'].'</strong> With RefCode :'.$id);
		//$this->save_log($hid, $modifieddata, $d1['what']);
		return array ('status'=>true,'myreason'=>$d1['what'].' successfully Updated');
	}
	
	public function get_equipment_list($filter='', $records='', $orderby='')
	{
		global $dbc;
		$out = array();
		$filterstr=$this->oo_filter($filter, $records, $orderby);
		$q = "SELECT *, DATE_FORMAT(created, '".MASKDATE."') AS created, DATE_FORMAT(due_date, '".MASKDATE."') AS due_date FROM master_equipment $filterstr";
		list($opt,$rs)= run_query($dbc,$q,$mode='multi',$msg='');
		if(!$opt) return $out;
		//$custId_map = get_my_reference_array('customer', 'custId', 'custname'); 
		while($row = mysqli_fetch_assoc($rs)){
			$id = $row['tmeId'];
			$out[$id] = $row; // storing the item id
			//$out[$id]['custId_val'] = $custId_map[$row['custId']]; 
			//$out[$id]['template_master_calbiration_head'] = $this->get_my_reference_array_direct("SELECT * FROM template_master_calbiration_head WHERE tmpmasterId = $id", 'tmchId');			
		}// while($row = mysqli_fetch_assoc($rs)){ ends
		return $out;	
	}
	
	public function equipment_delete($id, $filter='', $records='', $orderby='')
	{
		global $dbc;
		if(empty($filter)) $filter = "tmeId = $id";
		$out = array('status'=>false, 'myreason'=>'');
		$deleteRecord = $this->get_equipment_list($filter, $records, $orderby);
		if(empty($deleteRecord)){ $out['myreason'] = 'Master Equipment not found'; return $out;}
		//start the transaction
		mysqli_query($dbc, "START TRANSACTION");
		
		//Running the deletion queries
		$delquery = array();
		$delquery['master_equipment'] = "DELETE FROM master_equipment WHERE tmeId = $id";
		foreach($delquery as $key=>$value){
			if(!mysqli_query($dbc, $value)){
				mysqli_rollback($dbc);
				return array('status'=>false, 'myreason'=>'$key query failed');
			}
		}
		//After successfull deletion
		mysqli_commit($dbc);
		return array('status'=>true, 'myreason'=>'Master Equipment successfully deleted');
	}
}
?>