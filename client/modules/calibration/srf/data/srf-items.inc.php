<?php if (!defined('BASE_URL')) die('direct script access not allowed');?>
<?php

?>
<div>
  <table width="100%" border="1" style="border-collapse:collapse;">
    <tr style="font-weight:bold;">
      <td>Sr.No</td>
      <td>Item Description</td>
      <td>Party Id No.</td>
      <td>Make</td>
      <td>Model</td>
      <td>Sr.No</td>
      <td>Cal.Steps</td>
      <td>Range Size</td>
      <td>Least Count</td>
    </tr>
    <?php $inc =1; foreach($value['intake'] as $key1=>$value1){?>
    <tr>
      <td style="border:none; border-right:1px solid #000;"><?php echo $inc;?></td>
      <td style="border:none; border-right:1px solid #000;"><?php echo $value1['itemdesc'];?></td>
      <td style="border:none; border-right:1px solid #000;"><?php echo $value1['equipno'];?></td>
      <td style="border:none; border-right:1px solid #000;"><?php echo $value1['make'];?></td>
      <td style="border:none; border-right:1px solid #000;"><?php echo $value1['model'];?></td>
      <td style="border:none; border-right:1px solid #000;"><?php echo $value1['serial_no'];?></td>
      <td style="border:none; border-right:1px solid #000;"><?php echo $GLOBALS['cal_step_type'][$value1['cal_step_type']]; if(!empty($value1['cal_step_detail'])) echo '<br><span class="example">('.$value1['cal_step_detail'].')</span>';?></td>
      <td style="border:none; border-right:1px solid #000;"><?php echo $value1['range_size'];?></td>
      <td style="border:none; border-right:1px solid #000;"><?php echo $value1['least_count'];?></td> 
    </tr>
    <?php $inc++;} //foreach($value['intake'] as $key1=>$value1){ ends?>
    
    <?php for($i = count($value['intake']); $i<13; $i++){?>
    <tr>
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
