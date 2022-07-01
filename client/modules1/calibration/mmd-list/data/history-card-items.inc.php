<?php if (!defined('BASE_URL')) die('direct script access not allowed');?>
<?php
$equino = isset($_GET['equipno']) ? trim($_GET['equipno']) : 0;
$historystat = $myobj->get_history_card_detail($_SESSION[SESS.'id'], $equino);
if(!empty($historystat)){
$itemstat = $historystat['itemstat'];
$calibrationstat = $historystat['calibrationstat'];
?>
<!-- this portion indicate the print options -->
<div class="subhead1">
    <a href="javascript:printing('certificate_container');" title="take a printout" style="margin:0 10px;"><img src="../icon-system/i16X16/printo.png" /></a> Print History Card
</div>

<div id="certificate_container" style="padding:0px; font-size:62.5%; font-family:'Times New Roman', Times, serif; page-break-after:always; margin:0 auto; width:595pt; widthh:100%;">
  <div id="certificate_header">
    <table width="100%" class="header_table" style="margin-top:5pt; font-family:'Times New Roman', Times, serif; font-size:14pt; font-weight:bold; border-collapse:collapse;" border="1">
      <tr>
        <td align="center" style="font-size:17pt; padding:10pt; " ><?php echo $itemstat['partyId_val'];?></td>
      </tr>
      <tr>
        <td align="center" >MEASURING DEVICES HISTORY CARD</td>
      </tr>
    </table>
    
    <table width="100%" class="header_table" style="font-family:'Times New Roman', Times, serif; font-size:10pt; border-collapse:collapse;" border="1">
      <tr>
        <td colspan="2" ><strong>INSTRUMENT CODE :</strong> <?php echo $itemstat['equipno'];?></td>
        <td>&nbsp;</td>
        <td colspan="4" ><strong>SERIAL NO. :</strong> <?php echo $itemstat['serial_no'];?></td>
      </tr>
      <tr>
        <td colspan="2" ><strong>INSTRUMENT NAME :</strong> <?php echo $itemstat['itemdesc'];?></td>
        <td>&nbsp;</td>
        <td colspan="4" ><strong>ERROR :</strong> <?php //echo $itemstat['partyId_val'];?></td>
      </tr>
      <tr>
        <td colspan="2" ><strong>MAKE :</strong> <?php echo $itemstat['make'];?></td>
        <td>&nbsp;</td>
        <td colspan="4" ><strong>ERROR LIMIT :</strong> <?php //echo $itemstat['partyId_val'];?></td>
      </tr>
      <tr>
        <td colspan="2" ><strong>LEAST COUNT :</strong> <?php echo $itemstat['least_count'];?></td>
        <td>&nbsp;</td>
        <td colspan="4" ><strong>MASTER INST. USED :</strong> <?php //echo $itemstat['partyId_val'];?></td>
      </tr>
      <tr>
        <td colspan="2" ><strong>RANGE/SIZE :</strong> <?php echo $itemstat['range_size'];?></td>
        <td>&nbsp;</td>
        <td colspan="4" >&nbsp;</td>
      </tr>
      
      <tr style="font-weight:bold;">
        <td >DATE OF CAL.</td>
        <td>CALIBRATED BY</td>
        <td>CERT.NO.</td>
        <td>RESULT OF CALIBRATION.</td>
        <td>DUE DATE OF CAL.</td>
        <td>SIGNATURE</td>
        <td>REMARKS (IF ANY)</td>
      </tr>
      <?php $inc = 1; foreach($calibrationstat as $key=>$value){?>
      <tr>
        <td ><?php echo $value['calibration_date']; ?></td>
        <td><?php echo $value['calibrated_by']; ?></td>
        <td><?php echo $value['certificate_no']; ?></td>
        <td><?php echo $value['result']; ?></td>
        <td><?php echo $value['cal_due_date']; ?></td>
        <td><a class="printhide" title="Download Certificate" onclick="pdf_certificate(<?php echo $itemstat['obsId']; ?>);" href="javascript:void(0);"><img src="../icon-system/i16X16/download.png"></a></td>
        <td><?php echo $value['remark']; ?></td>
      </tr>
      <?php $inc++;} //foreach($calibrationstat as $key=>$value){?>
      
      <?php for($inc; $inc < 12; $inc++){?>
      <tr>
        <td >&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
      </tr>
      <?php } //for($inc; $inc < 15; $inc++){?>
    </table>
  </div> 
<?php }// if(!empty($historystat)){ 
	  else echo'<div><span class="warn">Sorry, <strong>History Card</strong> details <strong>not</strong> available.</span></div>'; ?>  
