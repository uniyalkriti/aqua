<?php if (!defined('BASE_URL')) die('direct script access not allowed');?>
<?php

?>
<div>
  <table width="100%" border="1" style="border-collapse:collapse;">
    <tr style="font-weight:bold;">
      <td class="printhide"> <input type="checkbox" id="myselect" onclick="selectCheckBoxes('myselect','myval[]');" /> <a href="javascript:void(0);" title="Download Selected Certificates" onclick="zip_all_certificate();"><img src="../icon-system/i16X16/download.png"></a></td>
      <td>Sr.No</td>
      <td>Item Description</td>
      <td>Party Id No.</td>
      <td>Make</td>
      <td>Model</td>
      <td>Sr.No</td>
      <td align="center">Schedule Process</td>
      <td align="center">Completion</td>
      <td align="center">Certificate</td>
    </tr>
    <?php $inc =1;
	foreach($value['intake'] as $key1=>$value1){
		//Making queries to view the schedule status and the allowed of certifiate download
		list($opt, $rs) = run_query($dbc, "SELECT DATE_FORMAT(sch_date, '".MASKDATE."') AS sch_datef, DATE_FORMAT(finish_date, '".MASKDATE."') AS finish_datef, sch_status FROM srf_item_schedule WHERE srfItemId = $key1 LIMIT 1", 'single');
		if($opt){
			$sch_process = '<img src="../icon-system/i16X16/yes.png"> <span class="example">('.$rs['sch_datef'].')</span>';
			$sch_done = $rs['sch_status'] == 2 ? '<img src="../icon-system/i16X16/yes.png"> <span class="example">('.$rs['finish_datef'].')</span>' : '<span style="color:red;">Pending</span>';
		}
		else{
			$sch_process = '<span style="color:red;">Pending</span>';
			$sch_done = '--';
		}
		//Checking whether the certificate is available for download or not
		list($opt1, $rs1) = run_query($dbc, "SELECT obsId FROM observation_sheet WHERE srfItemId = $key1 LIMIT 1", 'single');
		//$certificatelink = '<a class="iframef" title="Download Certificate" href="index.php?option=certificate&showmode=1&mode=1&id='.$rs1['obsId'].'&actiontype=print-certificate"><img src="../icon-system/i16X16/download.png"></a>';
		if($opt1)
			$certificatelink = '<a class="iframef" title="Download Certificate" onclick="pdf_certificate('.$rs1['obsId'].');" href="javascript:void(0);"><img src="../icon-system/i16X16/download.png"></a>';			
		else
			$certificatelink = '--';		
	?>
    <tr>
      <td class="printhide" style="border:none; border-right:1px solid #000;"><?php if(isset($rs1['obsId'])){?><input type="checkbox" name="myval[]" value="<?php echo $rs1['obsId'];?>"/><?php }else echo'&nbsp;';?></td>
      <td style="border:none; border-right:1px solid #000;"><?php echo $inc;?></td>
      <td style="border:none; border-right:1px solid #000;"><?php echo $value1['itemdesc'];?></td>
      <td style="border:none; border-right:1px solid #000;"><?php echo $value1['equipno'];?></td>
      <td style="border:none; border-right:1px solid #000;"><?php echo $value1['make'];?></td>
      <td style="border:none; border-right:1px solid #000;"><?php echo $value1['model'];?></td>
      <td style="border:none; border-right:1px solid #000;"><?php echo $value1['serial_no'];?></td>
      <td style="border:none; border-right:1px solid #000;" align="center"><?php echo $sch_process;?></td>
      <td style="border:none; border-right:1px solid #000;" align="center"><?php echo $sch_done;?></td>
      <td style="border:none; border-right:1px solid #000;" align="center"><?php echo $certificatelink;?></td> 
    </tr>
    <?php $inc++;} //foreach($value['intake'] as $key1=>$value1){ ends?>
    
    <?php for($i = count($value['intake']); $i<13; $i++){?>
    <tr>
      <td class="printhide" style="border:none; border-right:1px solid #000;">&nbsp;</td>
      <td style="border:none; border-right:1px solid #000;">&nbsp;</td>
      <td style="border:none; border-right:1px solid #000;">&nbsp;</td>
      <td style="border:none; border-right:1px solid #000;">&nbsp;</td>
      <td style="border:none; border-right:1px solid #000;">&nbsp;</td>
      <td style="border:none; border-right:1px solid #000;">&nbsp;</td>
      <td style="border:none; border-right:1px solid #000;">&nbsp;</td>
      <td style="border:none; border-right:1px solid #000;">&nbsp;</td>
      <td style="border:none; border-right:1px solid #000;">&nbsp;</td>
      <td style="border:none; border-right:1px solid #000;">&nbsp;</td>      
      
    </tr>
    <?php } //for($i = count($value['intake']); $i<7; $i++){ ends?>
  </table>
</div>  
