<?php 
class observation extends myfilter
{
	public $id = NULL;
	public $id_detail = NULL;
	
	public function __construct($id = NULL)
	{
		parent::__construct();
		$this->id = $id;
	}	
	
	public function get_observation_se_data()
	{
		/*$_POST['calibration_date'] = '06/01/2014'; $_POST['srfItemId'] = 3; $_POST['temperature'] = '20C'; 
		$_POST['humidity'] = '89'; $_POST['ref_standard'] = 'WI-03M';  
		$_POST['osr_head'] = $_POST['is_selected'] =  range(5,8);
		$_POST['osr_head_value0'] =  range(5,8);
		$_POST['osr_head_value1'] =  range(9,12);
		$_POST['osr_head_value2'] =  range(15,18);
		$_POST['osr_head_value3'] =  range(20,23);*/
		
 
		$d1 = array('calibration_date'=>$_POST['calibration_date'], 'cal_due_date'=>$_POST['cal_due_date'], 'srfItemId'=>$_POST['srfItemId'], 'certificate_no'=>$_POST['certificate_no'],  'temperature'=>$_POST['temperature'], 'humidity'=>$_POST['humidity'], 'ref_standard'=>$_POST['ref_standard'], 'cal_procedure'=>$_POST['cal_procedure'], 'cal_performed_at'=>$_POST['cal_performed_at'], 'location'=>$_POST['location'], 'visual_inspection'=>$_POST['visual_inspection'], 'repeatability'=>$_POST['repeatability'], 'parallelism'=>$_POST['parallelism'], 'uncertainity'=>$_POST['uncertainity'], 'cal_engineer'=>$_POST['cal_engineer'], 'technical_manager'=>$_POST['technical_manager'], 'uid'=>$_SESSION[SESS.'id'],'sesId'=>$_SESSION[SESS.'csess']);
		$d1['myreason'] = 'Please fill all the required information';
		$d1['what'] = 'Observation'; //whether to do history log or not
		return array(true,$d1);	
	}
	
	public function observation_save()
	{ 
		global $dbc;
		$out= array('status'=>'false','myreason'=>'');
		list($status,$d1)= $this ->get_observation_se_data();  
		if(!$status) return array('status'=>false, 'myreason'=>$d1['myreason']);		
		mysqli_query($dbc, "START TRANSACTION");
		
		//Date Manipulation
		$calibration_date = $cal_due_date = '';
		if(!empty($d1['calibration_date']))  $calibration_date = get_mysql_date($d1['calibration_date'], '/', false, false);
		//Calculating the due_date from the calibration date
		if(empty($d1['cal_due_date'])){
			$cf = $_POST['calibration_frequency']*30;
			$cal_due_date = strtotime(date("Y-m-d", strtotime($calibration_date)) . " +$cf day");
			$cal_due_date = date('Y-m-d', $cal_due_date);
		}else
			$cal_due_date =  get_mysql_date($d1['cal_due_date'], '/', false, false);
			
		//calculating the job no			 
		$jobno = $this->get_next_jobno();
		
		//Making the certificate no.
		$d1['certificate_no'] = $this->make_certno($d1['calibration_date']);
		
		$q = "INSERT INTO `observation_sheet` (`obsId`, `jobno`, `srfItemId`, `certificate_no`, `temperature`, `humidity`, `ref_standard`, `calibration_date`, `cal_due_date`, `cal_procedure`, `cal_performed_at`, `location`, `visual_inspection`, `repeatability`, `parallelism`, `uncertainity`, `cal_engineer`, `technical_manager`, `created`, `crId`) VALUES (NULL, $jobno, '$d1[srfItemId]', '$d1[certificate_no]', '$d1[temperature]', '$d1[humidity]', '$d1[ref_standard]', '$calibration_date', '$cal_due_date',  '$d1[cal_procedure]',  '$d1[cal_performed_at]',  '$d1[location]',  '$d1[visual_inspection]',  '$d1[repeatability]', '$d1[parallelism]',  '$d1[uncertainity]', '$d1[cal_engineer]',  '$d1[technical_manager]', NOW(), '$d1[uid]')";
		$r = mysqli_query($dbc, $q);
		if(!$r){mysqli_rollback($dbc); return array ('status'=>false, 'myreason'=>'Observation Table error') ;} 
		$rId = mysqli_insert_id($dbc);
		
		//Storing the master equipment details used in the observation
		$extrawork = $this->observation_store_master_equipment('save', $rId, array('eqpname'=>$_POST['eqpname'], 'eqpmake'=>$_POST['eqpmake'], 'certno'=>$_POST['certno'], 'traceability'=>$_POST['traceability'], 'due_date'=>$_POST['due_date'])); 
		if(!$extrawork['status']){mysqli_rollback($dbc); return array ('status'=>false, 'myreason'=>$extrawork['myreason']);} 
			
		//Storing the calibration result
		$extrawork = $this->observation_store_calib_result('save', $rId, array('osr_head'=>$_POST['osr_head'], 'rifId'=>$_POST['rifId'])); 
		if(!$extrawork['status']){mysqli_rollback($dbc); return array ('status'=>false, 'myreason'=>$extrawork['myreason']);} 
		
		//$extrawork = $this->observation_extra('save', $rId, array('osr_head'=>$_POST['osr_head'], 'is_selected'=>$_POST['is_selected'])); 
		mysqli_commit($dbc);
		//Logging the history
		history_log($dbc, 'Add', 'Observation received <b>'.$d1['srfItemId'].'</b> with With RefCode : '.$rId, $d1['what']);
		//Final success 
		return array ('status'=>true,'myreason'=>$d1['what'].' successfully Saved', 'rId'=>$rId);
	}
	
	//This function store/updates the master used for calibration
	public function observation_store_master_equipment($actiontype, $rId, $option=array())
	{ 
		global $dbc;	
		extract($option); // converting array key into individual variables
		$str = array();
		//If we are editing
		if($actiontype == 'edit') mysqli_query($dbc, "DELETE FROM observation_sheet_equipment WHERE obsId = $rId");
		
		// saving the details for the template_master_equipment table
		$un = 1;
		foreach($eqpname as $key=>$value){
			if(empty($value)) continue;
			if(!empty($due_date[$key])) $temp = get_mysql_date($due_date[$key], '/', false, false); else $temp = '';
			$unkey = $rId.$un;
			$str[] = "($unkey, $rId, '$value', '{$eqpmake[$key]}', '{$certno[$key]}', '$temp', '{$traceability[$key]}', '$key')";
			$un++;
		}
		$str = implode(', ', $str);	
		$q = "INSERT INTO `observation_sheet_equipment` VALUES $str";
		$r = mysqli_query($dbc, $q);
		if(!$r) return array ('status'=>false, 'myreason'=>'observation_sheet_equipment Table error') ;
		return array ('status'=>true,'myreason'=>'');
	}
	
	//This function store/updates the calibration results
	public function observation_store_calib_result($actiontype, $rId, $option)
	{ 
		global $dbc;	
		extract($option); // converting array key into individual variables
		$str = array();
		//If we are editing
		if($actiontype == 'edit') mysqli_query($dbc, "DELETE FROM observation_calibration_result WHERE obsId = $rId");
		
		//Eliminating the empty reading 
		$osr_head_value = array();
		//To store the result based on the format
		switch($rifId){
			case'2':
			case'4':
			case'1':
			{
				foreach($osr_head as $key=>$value){
					if(!empty($value)){
						$osr_head_value[$key]['osr_head'] = $value;
						for($i = 0; $i<count($_POST['osr_head_value'.$key]); $i++){
							if(!empty($_POST['osr_head_value'.$key][$i])) 
								$osr_head_value[$key]['osr_value'][] = $_POST['osr_head_value'.$key][$i];
						}
						//Deleting a step if its entry are not available
						if(!isset($osr_head_value[$key]['osr_value'])){
							unset($osr_head_value[$key]);
						}				
					}
				}//foreach($osr_head as $key=>$value){ ends
				break;
			}
			case'3':
			{
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
				$q = "INSERT INTO `observation_calibration_result` VALUES ($unkey, $rId, 'input3', '$temp')";
				if(mysqli_query($dbc,$q))
					return array('status'=>true, 'myreason'=>'Observation input successfully stored');
				else
					return array('status'=>false, 'myreason'=>'Observation input unserialize(base64_decode(temp) failed');
				break;
			}
		}// switch($rifId){ ends
		
		//Creating the database insert string
		$un = 1;
		foreach($osr_head_value as $key=>$value){
			$temp = implode('<$>',$value['osr_value']);
			$unkey = $rId.$un;
			$str[] = "($unkey, $rId, '{$value['osr_head']}', '$temp')";
			$un++;
		}
		$str = implode(', ', $str);	
		$q = "INSERT INTO `observation_calibration_result` VALUES $str";
		$r = mysqli_query($dbc, $q);
		if(!$r) return array ('status'=>false, 'myreason'=>'observation_calibration_result Table error') ;
		return array ('status'=>true,'myreason'=>'');
	}
	
	public function observation_extra($actiontype, $rId, $option)
	{ 
		global $dbc;	
		extract($option); // converting array key into individual variables
		$str = array();
		//Eliminating the empty reading 
		$osr_head_value = array();
		foreach($osr_head as $key=>$value){
			if(!empty($value)){
				$osr_head_value[$key]['osr_head'] = $value;
				$osr_head_value[$key]['is_selected'] = $_POST['is_selected'][$key];
				for($i = 0; $i<count($_POST['osr_head_value'.$key]); $i++){
					if(!empty($_POST['osr_head_value'.$key][$i])) 
						$osr_head_value[$key]['osr_value'][] = $_POST['osr_head_value'.$key][$i];
				}
				//Deleting a step if its entry are not available
				if(!isset($osr_head_value[$key]['osr_value'])){
					unset($osr_head_value[$key]);
				}				
			}
		}
		//Creating the database insert string
		foreach($osr_head_value as $key=>$value){
			$temp = implode('<$>',$value['osr_value']);
			$str[] = "(NULL, $rId, '{$value['osr_head']}', '$temp', '{$value['is_selected']}')";
		}
		$str = implode(', ', $str);	
		$q = "INSERT INTO `observation_sheet_reading` VALUES $str";
		$r = mysqli_query($dbc, $q);
		if(!$r) return array ('status'=>false, 'myreason'=>'observation_sheet_reading Table error') ;
		return array ('status'=>true,'myreason'=>'');
	}
	
	
	
    public function observation_edit($id)
	{
		global $dbc;
		$out = array('status'=>'false','myreason'=>'');
		list($status,$d1)=$this->get_observation_se_data();
		if(!$status) return array ('staus'=>false,'myreason'=>$d1['myreason']);
		//Checking whether the original data was modified or not
		/*$originaldata = $this->get_item_list("itemId = $id");
		$originaldata = $originaldata[$id];
		$modifieddata = $this->get_modified_data($originaldata, $d1);
		if(empty($modifieddata)) return array('status'=>false, 'myreason'=>'Please do <strong>atleast 1 change</strong> to update');*/
		
		//Date Manipulation
		$calibration_date = $cal_due_date = '';
		if(!empty($d1['calibration_date']))  $calibration_date = get_mysql_date($d1['calibration_date'], '/', false, false);
		//Calculating the due_date from the calibration date
		if(empty($d1['cal_due_date'])){
			$cf = $_POST['calibration_frequency']*30;
			$cal_due_date = strtotime(date("Y-m-d", strtotime($calibration_date)) . " +$cf day");
			$cal_due_date = date('Y-m-d', $cal_due_date);
		}else
			$cal_due_date =  get_mysql_date($d1['cal_due_date'], '/', false, false);
			
		//start the transaction
		mysqli_query($dbc, "START TRANSACTION");
		// query to update 		
		$q = "UPDATE observation_sheet SET certificate_no = '$d1[certificate_no]', temperature = '$d1[temperature]', humidity = '$d1[humidity]', ref_standard = '$d1[ref_standard]', calibration_date = '$calibration_date', cal_due_date = '$cal_due_date', cal_procedure = '$d1[cal_procedure]', cal_performed_at = '$d1[cal_performed_at]', location = '$d1[location]', visual_inspection = '$d1[visual_inspection]', repeatability = '$d1[repeatability]', parallelism = '$d1[parallelism]', uncertainity = '$d1[uncertainity]', cal_engineer = '$d1[cal_engineer]', technical_manager = '$d1[technical_manager]', modified = NOW(), mrId = $d1[uid] WHERE obsId='$id'";
		$r=mysqli_query($dbc, $q);
		if(!$r){mysqli_rollback($dbc); return array ('status'=>false, 'myreason'=>'observation_sheet Table error') ;} 
		$rId = $id;
		//Storing the master equipment details used in the observation
		$extrawork = $this->observation_store_master_equipment('edit', $rId, array('eqpname'=>$_POST['eqpname'], 'eqpmake'=>$_POST['eqpmake'], 'certno'=>$_POST['certno'], 'traceability'=>$_POST['traceability'], 'due_date'=>$_POST['due_date'])); 
		if(!$extrawork['status']){mysqli_rollback($dbc); return array ('status'=>false, 'myreason'=>$extrawork['myreason']);} 
			
		//Storing the calibration result
		$extrawork = $this->observation_store_calib_result('edit', $rId, array('osr_head'=>$_POST['osr_head'], 'rifId'=>$_POST['rifId'])); 
		if(!$extrawork['status']){mysqli_rollback($dbc); return array ('status'=>false, 'myreason'=>$extrawork['myreason']);}
		
		mysqli_commit($dbc);
		//Saving the user modification history
		$hid = history_log($dbc, 'Edit', 'Observation received <strong>'.$d1['srfItemId'].'</strong> With RefCode :'.$id);
		//$this->save_log($hid, $modifieddata, $d1['what']);
		return array ('status'=>true,'myreason'=>$d1['what'].' successfully Updated');
	}
	
	public function get_observation_list($filter='', $records='', $orderby='')
	{
		global $dbc;
		$out = array();
		$filterstr=$this->oo_filter($filter, $records, $orderby);
		$q = "SELECT observation_sheet.*, DATE_FORMAT(calibration_date, '".MASKDATE."') AS calibration_date, DATE_FORMAT(cal_due_date, '".MASKDATE."') AS cal_due_date, DATE_FORMAT(srfdate, '".MASKDATE."') AS srfdate, DATE_FORMAT(observation_sheet.created, '".MASKDATE."') AS created, srfcode, partyId , srf_item.*  FROM observation_sheet INNER JOIN srf_item USING(srfItemId) INNER JOIN srf USING(srfId) $filterstr";
		list($opt,$rs)= run_query($dbc,$q,$mode='multi',$msg='');
		if(!$opt) return $out;
		$custId_map = get_my_reference_array('party', 'partyId', 'party_name'); 
		while($row = mysqli_fetch_assoc($rs)){
			$id = $row['obsId'];
			$out[$id] = $row; // storing the item id
			$out[$id]['partyId_val'] = $custId_map[$row['partyId']]; 
		}// while($row = mysqli_fetch_assoc($rs)){ ends
		return $out;	
	}
	
	public function observation_delete($id, $filter='', $records='', $orderby='')
	{
		global $dbc;
		if(empty($filter)) $filter = "obsId = $id";
		$out = array('status'=>false, 'myreason'=>'');
		$deleteRecord = $this->get_observation_list($filter, $records, $orderby);
		if(empty($deleteRecord)){ $out['myreason'] = 'Observation not found'; return $out;}
		//start the transaction
		mysqli_query($dbc, "START TRANSACTION");
		
		//Running the deletion queries
		$delquery = array();
		$delquery['observation_sheet'] = "DELETE FROM observation_sheet WHERE obsId = $id LIMIT 1";
		$delquery['observation_sheet_equipment'] = "DELETE FROM observation_sheet_equipment WHERE obsId = $id";
		$delquery['observation_calibration_result'] = "DELETE FROM observation_calibration_result WHERE obsId = $id";
		$delquery['observation_sheet_reading'] = "DELETE FROM observation_sheet_reading WHERE obsId = $id";
		$srfitemId = $deleteRecord[$id]['srfItemId'];
		$delquery['observation_sheet_reading'] = "UPDATE srf_item SET tmpmasterId = 0 WHERE srfItemId = $srfitemId LIMIT 1";
		foreach($delquery as $key=>$value){
			if(!mysqli_query($dbc, $value)){
				mysqli_rollback($dbc);
				return array('status'=>false, 'myreason'=>'$key query failed');
			}
		}
		//After successfull deletion
		mysqli_commit($dbc);
		return array('status'=>true, 'myreason'=>'Observation successfully deleted');
	}
	
	public function get_next_jobno()
	{
		global $dbc;
		$srfdate = get_mysql_date($_POST['srfdate'], '/', false, false);
		list($opt,$rs) = run_query($dbc,"SELECT MAX(jobno) AS total FROM observation_sheet");
		return $rs['total']+1;
	}
	
	public function make_certno($calibrationdate)
	{
		global $dbc;
		$out = 'DMT/';
		$cd = explode('/', $calibrationdate);
		$year2digit = $cd[2]%100;
		$out .= $cd[0].month_to_alphabet($cd[1]).$year2digit;
		$calibration_date = get_mysql_date($calibrationdate, '/', false, true);
		list($opt,$rs) = run_query($dbc,"SELECT COUNT(obsId) AS total FROM observation_sheet WHERE DATE_FORMAT(calibration_date,'%Y%m%d') = '$calibration_date'");
		$no = $rs['total']+1;
		$no = str_pad($no, 3, "0", STR_PAD_LEFT);
		return $out.$no;
	}
	
	// Reading a certificate and return the formated array
	public function observation_equipment($actiontype, $id, $outmode='text')
	{
		global $dbc;
		if($actiontype == 'save')
			$q = "SELECT *, DATE_FORMAT(due_date, '".MASKDATE."') AS due_date FROM template_master_equipment WHERE tmpmasterId = $id";
		else
			$q = "SELECT *, DATE_FORMAT(due_date, '".MASKDATE."') AS due_date FROM observation_sheet_equipment WHERE obsId = $id";
		list($opt, $rs) = run_query($dbc, $q, 'multi');
		if(!$opt) return;	
			
		switch($outmode){
			case'text': // here we will read the data from the template_master_equipment
			{
				?>
                <table width="100%" class="searchlist">
                  <tr style="font-weight:bold;">
                    <td>Name</td>
                    <td>Make</td>
                    <td>Certificate</td>
                    <td>Calib. Due Date</td>
                    <td>Traceability</td>
                  </tr>
				  <?php while($row = mysqli_fetch_assoc($rs)){?>
                    <tr>
                      <td><input type="text" name="eqpname[]" value="<?php echo $row['eqpname'];?>" /></td>
                      <td><input type="text" name="eqpmake[]" value="<?php echo $row['eqpmake'];?>" /></td>
                      <td><input type="text" name="certno[]" value="<?php echo $row['certno'];?>" /></td>
                      <td><input type="text" name="due_date[]" class="qdatepicker" value="<?php echo $row['due_date'];?>" /></td>
                      <td><input type="text" name="traceability[]" value="<?php echo $row['traceability'];?>" /></td>
                    </tr>	
                  <?php }// while($row = mysqli_fetch_assoc($rs)){ ends ?>
                  </table>
                 <?php
				break;
			}
			
			case'html': // here we will read the data from the observation_sheet_equipment
			{
				$eqplabel = array('Name', 'Make', 'Cert. No.', 'Calib. Due Date', 'Traceability');
				$toteqp = array();
				while($row = mysqli_fetch_assoc($rs))
					$toteqp[] = $row['eqpname'].'<br>'.$row['eqpmake'].'<br>'.$row['certno'].'<br>'.$row['due_date'].'<br>'.$row['traceability'];
				?>
				<table width="100%" style="border-collapse:collapse; border:1px solid;">
                  <tr>
                    <td style="padding-left:10px; border-right: 1px solid;"><?php echo implode('<br>', $eqplabel);?></td>
                    <?php foreach($toteqp as $key=>$value){?>
                    <td style="padding-left:10px; border-right: 1px solid;"><?php echo $value;?></td>
                    <?php }?>
                  </tr>
                </table>
                <?php
				break;	
			}
		}// switch ends		
	}
	
	// Reading a certificate and return the formated array
	public function calibration_result_html($obsId, $rifId)
	{
		$if = new inputformat();
		$cf = $if->inputvalue($rifId, true, $obsId);
		switch($rifId){
			case 1:
			{
				?>
                <table width="100%" style="border-collapse:collapse;">
                    <tr>
                    <?php foreach($cf['osr_head'] as $key=>$value){?>
                        <td style="border:1px solid; padding-left:10px; text-align:center;"><?php echo $value;?></td>
                    <?php } ?>
                    </tr>
                    <?php for($i=0; $i< count($cf['osr_head_value0']); $i++){?>
                    <tr>	
                      <?php for($j=0; $j< count($cf['osr_head']); $j++){?>
                      <td style="border:1px solid; padding-left:10px; text-align:center;"><?php echo $cf['osr_head_value'.$j][$i]?></td>
                       <?php } ?>
                    </tr>    
                    <?php } ?>
                </table>
                <?php				
				break;	
			} // case 1: ends			
			case 2:
			{
				?>
                <table width="100%" style="border-collapse:collapse;">
                    <tr>
                      <td style="border:1px solid; padding:20px; text-align:center;"><?php echo $cf[0];?> : <?php echo $cf[1];?></td>
                      <td style="border:1px solid; padding:20px; text-align:center;"><?php echo $cf[2];?> : <?php echo $cf[3];?></td>
                    </tr>
                </table>
                <?php		
				break;	
			} // case 2: ends
			
			case 3:
			{
				?>
                <table width="100%" style="border-collapse:collapse; border:1px solid;">
				  <?php foreach($cf['osr_head'] as $key=>$value){?>
                  <tr>
                    <td colspan="4" style="border:1px solid; padding:7px; text-align:left; padding-left:15px;"><b><?php echo $value;?></b></td>
                  </tr>
                  <?php for($i=0; $i<count($cf['col'.$key.'1']); $i++){?>
                  <?php if($i == 0){?>
                  <tr style="font-weight:bold;">
                    <td style="border:1px solid; padding:5px; padding-left:15px;"><?php echo $cf['col'.$key.'1'][0];?></td>
                    <td style="border:1px solid; padding:5px; text-align:center;"><?php echo $cf['col'.$key.'2'][0];?></td>
                    <td style="border:1px solid; padding:5px; text-align:center;"><?php echo $cf['col'.$key.'3'][0];?></td>
                    <td style="border:1px solid; padding:5px; text-align:center;"><?php echo $cf['col'.$key.'4'][0];?></td>
                  </tr>
                  <?php }elseif($i == 1){ //if($i == 1){ ends?>
                  <tr>
                    <td style="border-right:1px solid; padding:5px; padding-left:15px;"><?php echo $cf['col'.$key.'1'][1];?></td>
                    <td style="border-right:1px solid; padding:5px; text-align:center;"><?php echo $cf['col'.$key.'2'][1];?></td>
                    <td style="border-right:1px solid; padding:5px; text-align:center;"><?php echo $cf['col'.$key.'3'][1];?></td>
                    <td style="border-right:1px solid; padding:5px; text-align:center;" rowspan="<?php echo count($cf['col'.$key.'1']) - 1;?>" valign="top"><?php echo $cf['col'.$key.'4'][1];?></td>
                  </tr>  
                  <?php }elseif($i > 1){ //elseif($i == 2){ ends){?>
                  <tr>
                    <td style="border-right:1px solid; padding:5px; padding-left:15px;"><?php echo $cf['col'.$key.'1'][$i];?></td>
                    <td style="border-right:1px solid; padding:5px; text-align:center;"><?php echo $cf['col'.$key.'2'][$i];?></td>
                    <td style="border-right:1px solid; padding:5px; text-align:center;"><?php echo $cf['col'.$key.'3'][$i];?></td>
                  </tr> 
                  <?php }//elseif($i > 2){ ends){
                  } // for($i=1; $i<count($cf['col'.$key.$i]); $i++){ ends
                  ?> 
                  <?php } // foreach($cf['osr_head'] as $key=>$value){ ends ?>
                </table>
                <?php		
				break;	
			} // case 3: ends
			
			case 4:
			{
				return $cf;	
				break;	
			} // case 4: ends
		}// switch($rifId){ ends
	}
	
	//This function will help in the mulitpage print
	public function print_looper($multiId, $options=array())
	{
		global $dbc;
		$out = array();
		if(is_null($multiId) || empty($multiId)) return $out;
		
		//Explode to get an array of all the Id
		$multiId = explode('-', $multiId);
		foreach($multiId as $key=>$value){
			$id = trim($value);
			if(empty($id)) continue;
			
			//read the record statistics
			$rcdstat = $this->get_my_reference_array_direct("SELECT srf_item.*, srfcode, DATE_FORMAT(srfdate, '".MASKDATE."') AS srfdate FROM srf_item INNER JOIN srf USING(srfId) WHERE srfItemId = $id LIMIT 1", 'srfItemId');
			if(empty($rcdstat)) continue;
			$out[$id] = $rcdstat[$id];
			$temp = $rcdstat[$id];
			
			//Calculation of the calibration steps if customer specific
			$stepmatched = false;
			$out[$id]['steps'] = array();
			if($temp['cal_step_type'] == 2 && !empty($temp['cal_step_detail'])){
				$stepmatched = true;
				$out[$id]['steps'] = explode(',', $temp['cal_step_detail']);
			}
			
			//We need to fetch the details of the template to which this item belongs
			if(empty($temp['tmpmasterId'])){
				$template_master_code = strtolower("{$temp['itemdesc']}--{$temp['range_size']}--{$temp['least_count']}");
				$q = "SELECT * FROM template_master WHERE concat_ws('--', item_name, range_size, least_count) = '$template_master_code' LIMIT 1";
			}else // if we already have a tempmasterId available use it
				$q = "SELECT * FROM template_master WHERE tmpmasterId = '{$temp['tmpmasterId']}' LIMIT 1";
				
			list($opt0,$rs0) = run_query($dbc, $q);
			$out[$id]['parallelism'] = $out[$id]['visual_inspection'] = $out[$id]['repeatibility'] = $out[$id]['location'] = $out[$id]['cal_performed_at'] = $out[$id]['ref_standard'] = $out[$id]['cal_procedure'] = '';
			if($opt0){
				$out[$id]['tmpmasterId'] = $rs0['tmpmasterId'];
				$out[$id]['parallelism'] = $rs0['parallelism'];
				$out[$id]['visual_inspection'] = $rs0['visual_inspection'];
				$out[$id]['repeatibility'] = $rs0['repeatability'];
				$out[$id]['location'] = $rs0['location'];
				$out[$id]['cal_performed_at'] = $rs0['cal_performed_at'];
				$out[$id]['ref_standard'] = $rs0['ref_standard'];
				$out[$id]['cal_procedure'] = $rs0['cal_procedure'];
				
				//If the calibration steps not found earlier then calculate them but only when the input format is 1
				if($rs0['rifId'] == 1 && !$stepmatched){
					list($opt1,$rs1) = run_query($dbc, "SELECT tmch_value FROM template_master_calbiration_head WHERE tmpmasterId = '{$rs0['tmpmasterId']}' AND sortorder = 0 LIMIT 1");
					if($opt1)
						$out[$id]['steps'] = explode('<$>', $rs1['tmch_value']);					
				}
			}
			
			//fetching the step details
		}
		//pre($out);
		return $out;
	}
}
?>