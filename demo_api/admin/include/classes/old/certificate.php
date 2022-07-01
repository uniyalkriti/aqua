<?php 
class certificate extends myfilter
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
		
 
		$d1 = array('calibration_date'=>$_POST['calibration_date'], 'due_date'=>$_POST['due_date'], 'srfItemId'=>$_POST['srfItemId'], 'certificate_no'=>$_POST['certificate_no'],  'temperature'=>$_POST['temperature'], 'humidity'=>$_POST['humidity'], 'ref_standard'=>$_POST['ref_standard'], 'cal_procedure'=>$_POST['cal_procedure'], 'location'=>$_POST['location'], 'visual_inspection'=>$_POST['visual_inspection'], 'repeatability'=>$_POST['repeatability'], 'parallelism'=>$_POST['parallelism'], 'uncertainity'=>$_POST['uncertainity'], 'cal_engineer'=>$_POST['cal_engineer'], 'technical_manager'=>$_POST['technical_manager'], 'input_format'=>$_POST['input_format'], 'uid'=>$_SESSION[SESS.'id'],'sesId'=>$_SESSION[SESS.'csess']);
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
		$calibration_date = $due_date = '';
		if(!empty($d1['calibration_date']))  $due_date = $calibration_date = get_mysql_date($d1['calibration_date'], '/', false, false);
		$jobno = $this->get_next_jobno();
		$q = "INSERT INTO `observation_sheet` (`obsId`, `jobno`, `srfItemId`, `certificate_no`, `temperature`, `humidity`, `ref_standard`, `calibration_date`, `due_date`, `cal_procedure`, `location`, `visual_inspection`, `repeatability`, `parallelism`, `uncertainity`, `cal_engineer`, `technical_manager`, `input_format`, `created`, `crId`) VALUES (NULL, $jobno, '$d1[srfItemId]', '$d1[certificate_no]', '$d1[temperature]', '$d1[humidity]', '$d1[ref_standard]', '$calibration_date', '$due_date',  '$d1[cal_procedure]',  '$d1[location]',  '$d1[visual_inspection]',  '$d1[repeatability]', '$d1[parallelism]',  '$d1[uncertainity]', '$d1[cal_engineer]',  '$d1[technical_manager]', '$d1[input_format]', NOW(), '$d1[uid]')";
		$r = mysqli_query($dbc, $q);
		if(!$r){mysqli_rollback($dbc); return array ('status'=>false, 'myreason'=>'Observation Table error') ;} 
		$rId = mysqli_insert_id($dbc);	
		$extrawork = $this->srf_extra('save', $rId, array('osr_head'=>$_POST['osr_head'], 'is_selected'=>$_POST['is_selected'])); 
		if(!$extrawork['status']){mysqli_rollback($dbc); return array ('status'=>false, 'myreason'=>$extrawork['myreason']);} 
		mysqli_commit($dbc);
		//Logging the history
		history_log($dbc, 'Add', 'Observation received <b>'.$d1['srfItemId'].'</b> with With RefCode : '.$rId, $d1['what']);
		//Final success 
		return array ('status'=>true,'myreason'=>$d1['what'].' successfully Saved', 'rId'=>$rId);
	}
	
	public function srf_extra($actiontype, $rId, $option)
	{ 
		global $dbc;	
		extract($option); // converting array key into individual variables
		$str = array();
		//during update we are required to remove the previous entry
		/*if($actiontype == 'update'){
			mysqli_query($dbc, "DELETE FROM srf_item WHERE srfId = $rId");
			//Fetching the previous uncode
			list($opt, $rs) = run_query($dbc, "SELECT uncode, qty FROM sale_item_uncode WHERE saleId = $rId", 'multi');
			if($opt){
				while($row = mysqli_fetch_assoc($rs)){
					$q = "UPDATE stock_item SET soldunit = soldunit - $row[qty], balance = balance + $row[qty] WHERE uncode = '$row[uncode]' LIMIT 1";
					if(!mysqli_query($dbc, $q)){ //// If we were not able to restore the qty back
						return array('status'=>false, 'myreason'=>$q);
					}	
				}//while($row = mysqli_fetch_assoc($rs)){
			}//if($opt){
			//Deleting the data from sale_item_uncode
			mysqli_query($dbc, "DELETE FROM sale_item_uncode WHERE saleId = $rId");	
		}*/
		// saving the details for the stock item table
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
	
    public function order_edit($id)
	{
		global $dbc;
		$out = array('status'=>'false','myreason'=>'');
		list($status,$d1)=$this->get_order_se_data();
		if(!$status) return array ('staus'=>false,'myreason'=>$d1['myreason']);
		//Checking whether the original data was modified or not
		/*$originaldata = $this->get_item_list("itemId = $id");
		$originaldata = $originaldata[$id];
		$modifieddata = $this->get_modified_data($originaldata, $d1);
		if(empty($modifieddata)) return array('status'=>false, 'myreason'=>'Please do <strong>atleast 1 change</strong> to update');*/
		//start the transaction
		mysqli_query($dbc, "START TRANSACTION");
		// query to update 
		$billdate = '';
		if(!empty($d1['saledate']))  $billdate = get_mysql_date($d1['saledate'], '/', false, false);
		$q = "UPDATE sale SET transportation = '$d1[transportation]', remark = '$d1[remark]', saledate = '$billdate', invoice = '$d1[invoice]', custId = '$d1[custId]', modified = NOW(), mrId = $d1[uid] WHERE saleId='$id'";
		$r=mysqli_query($dbc, $q);
		if(!$r){mysqli_rollback($dbc); return array ('status'=>false, 'myreason'=>'stock Table error') ;} 
		$extrawork = $this->order_extra('update', $id, $_POST['uncode'], $_POST['discountperc'], $_POST['discountflat'], $_POST['itemId'], $_POST['qty'], $_POST['dispatchqty'], $_POST['rate'], $_POST['taxId'], $_POST['taxamount'], $_POST['taxvalue'], $_POST['taxtype']); 
		if(!$extrawork['status']){mysqli_rollback($dbc); return array ('status'=>false, 'myreason'=>$extrawork['myreason']);} 
		mysqli_commit($dbc);
		//Saving the user modification history
		$hid = history_log($dbc, 'Edit', 'sale received <strong>'.$d1['invoice'].'</strong> With RefCode :'.$id);
		//$this->save_log($hid, $modifieddata, $d1['what']);
		return array ('status'=>true,'myreason'=>$d1['what'].' successfully Updated');
	}
	
	public function get_observation_list($filter='', $records='', $orderby='')
	{
		global $dbc;
		$out = array();
		$filterstr=$this->oo_filter($filter, $records, $orderby);
		$q = "SELECT observation_sheet.*, DATE_FORMAT(calibration_date, '".MASKDATE."') AS calibration_date, DATE_FORMAT(due_date, '".MASKDATE."') AS due_date, DATE_FORMAT(srfdate, '".MASKDATE."') AS srfdate, DATE_FORMAT(observation_sheet.created, '".MASKDATE."') AS created, srfcode, lab_code, partyId   FROM observation_sheet INNER JOIN srf_item USING(srfItemId) INNER JOIN srf USING(srfId) $filterstr";
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
	
	public function order_delete($id, $filter='', $records='', $orderby='')
	{
		global $dbc;
		if(empty($filter)) $filter = "saleId = $id";
		$out = array('status'=>false, 'myreason'=>'');
		$deleteRecord = $this->get_order_list($filter, $records, $orderby);
		if(empty($deleteRecord)){ $out['myreason'] = 'Invoice not found'; return $out;}
		//start the transaction
		mysqli_query($dbc, "START TRANSACTION");
		//Restoring the unsold item back
		foreach($deleteRecord[$id]['sale_item_uncode'] as $key=>$value){
			$q = "UPDATE stock_item SET soldunit = soldunit - $value[qty], balance = balance + $value[qty] WHERE uncode = '$key' LIMIT 1";
			if(!mysqli_query($dbc, $q)){ //// If we were not able to restore the qty back
				mysqli_rollback($dbc);
				//return array('status'=>false, 'myreason'=>'Qty restoration failed'.$q);
				return array('status'=>false, 'myreason'=>$q);
			}				
		}
		
		//Running the deletion queries
		$delquery = array();
		$delquery['sale'] = "DELETE FROM sale WHERE saleId = $id LIMIT 1";
		$delquery['sale_item'] = "DELETE FROM sale_item WHERE saleId = $id";
		$delquery['sale_item_uncode'] = "DELETE FROM sale_item_uncode WHERE saleId = $id";
		$delquery['sale_tax'] = "DELETE FROM sale_tax WHERE saleId = $id";
		foreach($delquery as $key=>$value){
			if(!mysqli_query($dbc, $value)){
				mysqli_rollback($dbc);
				return array('status'=>false, 'myreason'=>'$key query failed');
			}
		}
		//After successfull deletion
		mysqli_commit($dbc);
		return array('status'=>true, 'myreason'=>'Invoice successfully deleted');
	}
	
	public function price_selection($itemId, $saleId)
	{
		global $dbc;
		$out = array();
		$q = "SELECT * FROM stock_item WHERE itemId = $itemId AND balance > 0 ORDER BY uncode ASC";
		//if we are editing
		if($saleId)
			$q = "SELECT * FROM stock_item WHERE itemId = $itemId AND (balance > 0 OR uncode IN (SELECT uncode FROM sale_item_uncode WHERE saleId = $saleId AND itemId = $itemId)) ORDER BY uncode ASC";
		list($opt,$rs)= run_query($dbc, $q,$mode='multi',$msg='');
		if(!$opt) return $out;
		//if we are editing
		$salestat = array();
		if($saleId){
			$salestat = $this->get_order_list("saleId = $saleId");
			if(!empty($salestat)) $salestat = $salestat[$saleId];
		}
		
		while($row = mysqli_fetch_assoc($rs)){
			$row['rate'] = round($row['rate']);
			if(isset($salestat['sale_item_uncode'][$row['uncode']])){ // to add the item if we are editing
				$row['balance'] += $salestat['sale_item_uncode'][$row['uncode']]['qty'];
				$out[$itemId][$row['rate']]['prefill'][] = $salestat['sale_item_uncode'][$row['uncode']]['qty'];
			}
			else
				$out[$itemId][$row['rate']]['prefill'][] = 0;
				
			$out[$itemId][$row['rate']]['uncode'][] = $row['uncode'];
			$out[$itemId][$row['rate']]['balance'][] = $row['balance'];
		}// while($row = mysqli_fetch_assoc($rs)){ ends		
		return $out;	
	}
	
	public function price_uncode_grouping($itemId, $saleId)
	{
		global $dbc;
		$out = array();
		$price_range = $this->price_selection($itemId, $saleId);
		if(empty($price_range)) return $out;
		$stock = array();
		foreach($price_range[$itemId] as $key => $value){
			$stock[$key]['uncode'] = implode(',', $value['uncode']);
			$stock[$key]['uncodebalance'] = implode(',', $value['balance']);
			$stock[$key]['balance'] = array_sum($value['balance']);
			$stock[$key]['prefill'] = array_sum($value['prefill']);
		}
		return $stock;	
	}
	
	public function price_display_div($itemId, $saleId=NULL)
	{
		global $dbc;
		$out = '';
		$price_uncode_grouping = $this->price_uncode_grouping($itemId, $saleId);
		//if(empty($price_uncode_grouping)) return $out;
		$out .= '
        <div style="height:70px; overflow:auto; background-color:#FC3;">
          <table>
            <tr style="font-weight:bold;">
              <td>Price</td>
              <td>Stock</td>
              <td>Out</td>
            </tr>';
			$prefill = 0;
            foreach($price_uncode_grouping as $key=>$value){
				$prefill += $value['prefill'];
				if($value['prefill'] < 1) $value['prefill'] = ''; // during edit only
            	$out .='
			<tr>
              <td><input type="hidden" name="uncode[]" value="'.$value['uncode'].'" />
			  <input type="hidden" name="uncodebalance[]" value="'.$value['uncodebalance'].'" />
                <input type="hidden" name="itemIdstockId[]" value="'.$itemId.'" />
                '.$key.'<input type="hidden" name="itemIdstockprice[]" value="'.$key.'" />
              </td>
              <td>'.$value['balance'].' Pcs<input type="hidden" name="itemIdstockqty[]" value="'.$value['balance'].'" /></td>
              <td><input type="text" style="width:30px;" name="itemIdstockqtyout[]" value="'.$value['prefill'].'" onkeyup="do_sum_price_selection_qty();" onkeypress="return isNumberKeyOrFloat(event);"/></td>
            </tr>';
			}// foreach($price_uncode_grouping as $key=>$value) ends
			$extra = 0;
			if($saleId){
				list($opt, $rs) = run_query($dbc,"SELECT qty FROM sale_item WHERE itemId = $itemId AND saleId = $saleId LIMIT 1");
				if($opt) $extra = $rs['qty'] - $prefill;
			}
			if($extra < 1) $extra = '';
			$out .='
            <tr>
              <td colspan="2" align="right">Extra Out : </td>
              <td>
			    <input type="hidden" name="extraoutItemId[]" value="'.$itemId.'" />
				<input type="text" style="width:30px;"  name="extraout[]" value="'.$extra.'" onkeyup="do_sum_price_selection_qty();" onkeypress="return isNumberKeyOrFloat(event);"/>
			  
			  </td>
            </tr>
          </table>
        </div>';
        return $out;
	}
	
	public function get_next_jobno()
	{
		global $dbc;
		$srfdate = get_mysql_date($_POST['srfdate'], '/', false, false);
		list($opt,$rs) = run_query($dbc,"SELECT MAX(jobno) AS total FROM observation_sheet')");
		return $rs['total']+1;
	}
	
	public function get_average_sale_price($itemId)
	{
		global $dbc;
		$out = 0;
		list($opt,$rs) = run_query($dbc,"SELECT SUM(rate*balance) AS total, SUM(balance) AS qty FROM stock_item WHERE itemId = $itemId AND balance > 0");
		if($opt && $rs['qty'] != 0) $out = 	ceil($rs['total']/$rs['qty']);
		return $out;
	}
}
?>