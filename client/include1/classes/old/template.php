<?php 
class template extends myfilter
{
	public $id = NULL;
	public $id_detail = NULL;
	
	public function __construct($id = NULL)
	{
		parent::__construct();
		$this->id = $id;
	}	
	
	public function get_template_master_se_data()
	{
		/*$_POST['item_name'] = $_POST['ref_standard'] = $_POST['cal_procedure'] = $_POST['rifId'] = $_POST['format_no'] = $_POST['range_size'] = $_POST['least_count'] = $_POST['unit'] = $_POST['cal_performed_at'] = $_POST['location'] = $_POST['temperature'] = $_POST['humidity'] = $_POST['visual_inspection'] = $_POST['repeatability'] = $_POST['parallelism'] = $_POST['cal_engineer'] = $_POST['uncertainity'] = $_POST['technical_manager'] = $_POST['remark'] = 1;
		
		$_POST['eqpname'] =  $_POST['eqpmake'] =  $_POST['certno'] =  $_POST['traceability'] =  range(20,23);
		$_POST['due_date'] =  array('01/01/2014', '02/01/2014', '03/01/2014', '04/01/2014');
		
		$_POST['top_row_label'] =  array('label1', 'label2', 'label3', 'label4');
		$_POST['col0'] =  range(1,5);
		$_POST['col1'] =  range(1,5);
		$_POST['col2'] =  range(1,5);
		$_POST['col3'] =  range(1,5);	*/	
		if(!isset($_POST['osr_head'])) $_POST['osr_head'] = array();
 
		$d1 = array('item_name'=>$_POST['item_name'], 'ref_standard'=>$_POST['ref_standard'], 'cal_procedure'=>$_POST['cal_procedure'], 'rifId'=>$_POST['rifId'], 'format_no'=>$_POST['format_no'], 'range_size'=>$_POST['range_size'], 'least_count'=>$_POST['least_count'], 'unit'=>$_POST['unit'], 'cal_performed_at'=>$_POST['cal_performed_at'], 'location'=>$_POST['location'], 'temperature'=>$_POST['temperature'], 'humidity'=>$_POST['humidity'], 'visual_inspection'=>$_POST['visual_inspection'], 'repeatability'=>$_POST['repeatability'], 'parallelism'=>$_POST['parallelism'], 'cal_engineer'=>$_POST['cal_engineer'], 'technical_manager'=>$_POST['technical_manager'], 'uncertainity'=>$_POST['uncertainity'], 'remark'=>$_POST['remark'],	'uid'=>$_SESSION[SESS.'id'],'sesId'=>$_SESSION[SESS.'csess']);
		$d1['myreason'] = 'Please fill all the required information';
		$d1['what'] = 'Template'; //whether to do history log or not
		return array(true,$d1);	
	}
	
	public function template_master_save()
	{ 
		global $dbc;
		//return $this->template_custom_input3(3);
		$out= array('status'=>'false','myreason'=>'');
		list($status,$d1)= $this ->get_template_master_se_data();  
		if(!$status) return array('status'=>false, 'myreason'=>$d1['myreason']);		
		mysqli_query($dbc, "START TRANSACTION");
		$calibration_date = '';
		$d1['template_master_code'] = $template_master_code = "$d1[item_name] --> Range [$d1[range_size]] --> Least Count [$d1[least_count]] --> Unit [$d1[unit]]";
		//if(!empty($d1['calibration_date']))  $calibration_date = get_mysql_date($d1['calibration_date'], '/', false, false);
		$q = "INSERT INTO `template_master` (`tmpmasterId`, `template_master_code`, `item_name`, `ref_standard`, `cal_procedure`, `rifId`,  `format_no`, `range_size`, `least_count`, `unit`, `cal_performed_at`, `location`, `temperature`, `humidity`, `visual_inspection`, `repeatability`, `parallelism`, `cal_engineer`, `uncertainity`, `technical_manager`, `remark`, `created`, `crId`)
				
		 VALUES (NULL, '$template_master_code', '$d1[item_name]', '$d1[ref_standard]', '$d1[cal_procedure]', '$d1[rifId]', '$d1[format_no]', '$d1[range_size]', '$d1[least_count]', '$d1[unit]', '$d1[cal_performed_at]', '$d1[location]', '$d1[temperature]', '$d1[humidity]', '$d1[visual_inspection]', '$d1[repeatability]', '$d1[parallelism]', '$d1[cal_engineer]', '$d1[uncertainity]', '$d1[technical_manager]', '$d1[remark]', NOW(), '$d1[uid]')";
		 
		$r = mysqli_query($dbc, $q);
		if(!$r){mysqli_rollback($dbc); return array ('status'=>false, 'myreason'=>'Observation Table error') ;} 
		$rId = mysqli_insert_id($dbc);
		$extrawork = $this->template_master_extra('save', $rId, array('eqpname'=>$_POST['eqpname'], 'eqpmake'=>$_POST['eqpmake'], 'certno'=>$_POST['certno'], 'traceability'=>$_POST['traceability'], 'due_date'=>$_POST['due_date'], 'osr_head'=>$_POST['osr_head'])); 
		if(!$extrawork['status']){mysqli_rollback($dbc); return array ('status'=>false, 'myreason'=>$extrawork['myreason']);} 
		mysqli_commit($dbc);
		//Logging the history
		history_log($dbc, 'Add', 'Template received <b>'.$d1['template_master_code'].'</b> with With RefCode : '.$rId, $d1['what']);
		//Final success 
		return array ('status'=>true,'myreason'=>$d1['what'].' successfully Saved', 'rId'=>$rId);
	}
	
	public function template_master_extra($actiontype, $rId, $option)
	{ 
		global $dbc;	
		extract($option); // converting array key into individual variables
		$str = array();
		//If we are editing
		if($actiontype == 'edit'){
			mysqli_query($dbc, "DELETE FROM template_master_equipment WHERE tmpmasterId = $rId");
			mysqli_query($dbc, "DELETE FROM template_master_calbiration_head WHERE tmpmasterId = $rId");
		}
		
		// saving the details for the template_master_equipment table
		$un = 1;
		foreach($eqpname as $key=>$value){
			if(!empty($due_date[$key])) $temp = get_mysql_date($due_date[$key], '/', false, false); else $temp = '';
			$unkey = $rId.$un;
			$str[] = "($unkey, $rId, '$value', '{$eqpmake[$key]}', '{$certno[$key]}', '$temp', '{$traceability[$key]}', '$key')";
			$un++;
		}
		$str = implode(', ', $str);	
		$q = "INSERT INTO `template_master_equipment` VALUES $str";
		$r = mysqli_query($dbc, $q);
		if(!$r) return array ('status'=>false, 'myreason'=>'template_master_equipment Table error') ;
		
		//FOR THE CASE OF ELECTRICAL EQUIPMENTS WE ARE CHANGING THE CODE PARITALLY
		if($_POST['rifId'] == 3){
			$funcToCall = 'template_custom_input'.$_POST['rifId'];
			return $this->$funcToCall($rId);
		}
		
		//Eliminating the empty reading 
		$osr_head_value = array();
		foreach($osr_head as $key=>$value){
			if(!empty($value)){
				$osr_head_value[$key]['top_row_label'] = $value;
				for($i = 0; $i<count($_POST['osr_head_value'.$key]); $i++){
					if(!empty($_POST['osr_head_value'.$key][$i])) 
						$osr_head_value[$key]['col_value'][] = $_POST['osr_head_value'.$key][$i];
				}
				//Deleting a step if its entry are not available
				if(!isset($osr_head_value[$key]['top_row_label'])){
					unset($osr_head_value[$key]);
				}				
			}
		}
		//Creating the database insert string
		$str = array();
		$un = 1;
		foreach($osr_head_value as $key=>$value){
			$temp = isset($value['col_value']) ? implode('<$>',$value['col_value']) : '';
			$unkey = $rId.$un;
			$str[] = "($unkey, $rId, '{$value['top_row_label']}', '$temp', '$key')";
			$un++;
		}
		$str = implode(', ', $str);	
		$q = "INSERT INTO `template_master_calbiration_head` VALUES $str";
		$r = mysqli_query($dbc, $q);
		if(!$r) return array ('status'=>false, 'myreason'=>'template_master_calbiration_head Table error') ;
		return array ('status'=>true,'myreason'=>'');
	}
	
    public function template_master_edit($id)
	{
		global $dbc;
		$out = array('status'=>'false','myreason'=>'');
		list($status,$d1)=$this->get_template_master_se_data();
		if(!$status) return array ('staus'=>false,'myreason'=>$d1['myreason']);
		//Checking whether the original data was modified or not
		/*$originaldata = $this->get_item_list("itemId = $id");
		$originaldata = $originaldata[$id];
		$modifieddata = $this->get_modified_data($originaldata, $d1);
		if(empty($modifieddata)) return array('status'=>false, 'myreason'=>'Please do <strong>atleast 1 change</strong> to update');*/
		//start the transaction
		mysqli_query($dbc, "START TRANSACTION");
		// query to update 
		$d1['template_master_code'] = $template_master_code = "$d1[item_name] --> Range [$d1[range_size]] --> Least Count [$d1[least_count]] --> Unit [$d1[unit]]";
		$q = "UPDATE template_master SET template_master_code = '$d1[template_master_code]', item_name = '$d1[item_name]', ref_standard = '$d1[ref_standard]', cal_procedure = '$d1[cal_procedure]', rifId = '$d1[rifId]', format_no = '$d1[format_no]', range_size = '$d1[range_size]', least_count = '$d1[least_count]', unit = '$d1[unit]', cal_performed_at = '$d1[cal_performed_at]', location = '$d1[location]', temperature = '$d1[temperature]', humidity = '$d1[humidity]', visual_inspection = '$d1[visual_inspection]', repeatability = '$d1[repeatability]', parallelism = '$d1[parallelism]', cal_engineer = '$d1[cal_engineer]', uncertainity = '$d1[uncertainity]', technical_manager = '$d1[technical_manager]', remark = '$d1[remark]', modified = NOW(), mrId = $d1[uid] WHERE tmpmasterId='$id'";
		$r=mysqli_query($dbc, $q);
		if(!$r){mysqli_rollback($dbc); return array ('status'=>false, 'myreason'=>'template_master error') ;} 
		$rId = $id;
		$extrawork = $this->template_master_extra('edit', $rId, array('eqpname'=>$_POST['eqpname'], 'eqpmake'=>$_POST['eqpmake'], 'certno'=>$_POST['certno'], 'traceability'=>$_POST['traceability'], 'due_date'=>$_POST['due_date'], 'osr_head'=>$_POST['osr_head'])); 
		if(!$extrawork['status']){mysqli_rollback($dbc); return array ('status'=>false, 'myreason'=>$extrawork['myreason']);} 
		mysqli_commit($dbc);
		//Saving the user modification history
		$hid = history_log($dbc, 'Edit', 'Template received <strong>'.$d1['template_master_code'].'</strong> With RefCode :'.$id);
		//$this->save_log($hid, $modifieddata, $d1['what']);
		return array ('status'=>true,'myreason'=>$d1['what'].' successfully Updated');
	}
	
	public function get_template_master_list($filter='', $records='', $orderby='')
	{
		global $dbc;
		$out = array();
		$filterstr=$this->oo_filter($filter, $records, $orderby);
		$q = "SELECT *, DATE_FORMAT(created, '".MASKDATE."') AS created FROM template_master $filterstr";
		list($opt,$rs)= run_query($dbc,$q,$mode='multi',$msg='');
		if(!$opt) return $out;
		//$custId_map = get_my_reference_array('customer', 'custId', 'custname'); 
		while($row = mysqli_fetch_assoc($rs)){
			$id = $row['tmpmasterId'];
			$out[$id] = $row; // storing the item id
			//$out[$id]['custId_val'] = $custId_map[$row['custId']]; 
			$out[$id]['template_master_equipment'] = $this->get_my_reference_array_direct("SELECT *, DATE_FORMAT(due_date, '".MASKDATE."') AS due_date FROM template_master_equipment WHERE tmpmasterId = $id", 'tmeId');
			$out[$id]['template_master_calbiration_head'] = $this->get_my_reference_array_direct("SELECT * FROM template_master_calbiration_head WHERE tmpmasterId = $id", 'tmchId');			
		}// while($row = mysqli_fetch_assoc($rs)){ ends
		return $out;	
	}
	
	public function template_master_delete($id, $filter='', $records='', $orderby='')
	{
		global $dbc;
		if(empty($filter)) $filter = "tmpmasterId = $id";
		$out = array('status'=>false, 'myreason'=>'');
		$deleteRecord = $this->get_template_master_list($filter, $records, $orderby);
		if(empty($deleteRecord)){ $out['myreason'] = 'Template not found'; return $out;}
		//start the transaction
		mysqli_query($dbc, "START TRANSACTION");		
		//Running the deletion queries
		$delquery = array();
		$delquery['template_master_calbiration_head'] = "DELETE FROM template_master_calbiration_head WHERE tmpmasterId = $id";
		$delquery['template_master_equipment'] = "DELETE FROM template_master_equipment WHERE tmpmasterId = $id";
		$delquery['template_master'] = "DELETE FROM template_master WHERE tmpmasterId = $id  LIMIT 1";
		foreach($delquery as $key=>$value){
			if(!mysqli_query($dbc, $value)){
				mysqli_rollback($dbc);
				return array('status'=>false, 'myreason'=>'$key query failed');
			}
		}
		//After successfull deletion
		mysqli_commit($dbc);
		return array('status'=>true, 'myreason'=>'Template successfully deleted');
	}
	
	public function template_custom_input3($rId)
	{
		global $dbc;
		$total_col = count($_POST['osr_head']);
		$max_col_allowed = 12;
		$cf = array();
		foreach($_POST['osr_head'] as $key=>$value){
			if(!empty($value)) $cf['osr_head'][] = $value;
		}
		
		$found = 0;
		for($i = 0; $i<$max_col_allowed; $i++){
			if(empty($_POST['osr_head'][$i])) continue; // id main head row is not available, no need to count the rows
			if(isset($_POST['col'.$i.'1'])){
				for($j=0; $j<count($_POST['col'.$i.'1']); $j++){
					if(empty($_POST['col'.$i.'1'][$j])) continue;
					$cf['col'.$found.'1'][] = $_POST['col'.$i.'1'][$j];
					$cf['col'.$found.'2'][] = $_POST['col'.$i.'2'][$j];
					$cf['col'.$found.'3'][] = $_POST['col'.$i.'3'][$j];
					//as we have to boxes for uncertainity column value
					if($j < 2)
						$cf['col'.$found.'4'][] = $_POST['col'.$j.'4'][$j];
				}
				$found++;				
			}// if(isset($_POST['col'.$i.'1'])){ ends			
		}
		$temp = base64_encode(serialize($cf));
		$unkey = $rId.'1';
		//pre(unserialize(base64_decode($temp)));
		$q = "INSERT INTO `template_master_calbiration_head` VALUES ($unkey, $rId, 'input3', '$temp', '1')";
		if(mysqli_query($dbc,$q))
			return array('status'=>true, 'myreason'=>'Template input successfully stored');
		else
			return array('status'=>false, 'myreason'=>'Template input unserialize(base64_decode(temp) failed');
		
	}
}
?>