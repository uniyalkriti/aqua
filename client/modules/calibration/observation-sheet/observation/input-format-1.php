<?php if (!defined('BASE_URL')) die('direct script access not allowed');?>
<?php 
$osr_head = 13;
$osr_head_value = 7;
$steps = $value['steps'];

?>
<table width="100%"  style="border-collapse:collapse; border:1px solid #000; font-size:12px;">
  <tr>
    <!--<td>&nbsp;</td>-->
    <td style="width:4%; border-bottom:1px solid #000; border-right:1px solid #000;"><strong>S.No.</strong></td>
    <td style="width:8%; border-bottom:1px solid #000; border-right:1px solid #000;"><strong>UUC IN</strong></td>    
    <?php for($j = 0; $j <$osr_head_value; $j++){?>
    <td style="border-bottom:1px solid #000; border-right:1px solid #000;">&nbsp;</td>
    <?php } //for($j = 0; $i <$osr_head_value; $j++){ ends?>
    <td style="width:18%; border-bottom:1px solid #000;"><strong>UNCERTAINITY <Br />(AT 95% CL)</strong></td>
  </tr>
  <?php for($i = 0; $i <$osr_head; $i++){?>
  <tr>
    <!--<td><?php arr_pulldown('is_selected[]', array('N','Y'), '', true, false); ?></td>-->
    <td><?php //echo $i+1;?></td>
    <td style="border-left:1px solid #000; border-right:1px solid #000;"><?php if(isset($steps[$i])) echo $steps[$i]; else echo'&nbsp;'; ?></td>
    <?php for($j = 0; $j <$osr_head_value; $j++){?>
    <td style="border-left:1px solid #000; border-right:1px solid #000;">&nbsp;</td>
    <?php } //for($j = 0; $i <$osr_head_value; $j++){ ends?>
    <?php if($i == 0){?>
    <td rowspan="<?php echo $osr_head;?>" valign="top" style="border-left:1px solid #000; border-right:1px solid #000;">
    	A) NORMAL VALUE : ______________<br /><br />
        B) TYPE 'A' READINGS :<br />
        1. ________________<br />
        2. ________________<br />
        3. ________________<br />
        4. ________________<br />
        5. ________________<br />
        6. ________________<br />
        7. ________________<br />
        8. ________________<br />
        9. ________________<br />
        10. ________________<br />
        
        A) CALCULATED TYPE 'A'<br />
        UNCERTAINITY : _______<br /><br />
        
        B) CALCULATED TYPE 'B'<br />
        UNCERTAINITY : _______<br /><br />
        
        B) CALCULATED EXPANDED UNCERATINTIY AS PER <br />SOFTWARE : _______<br />
    </td>
    <?php }//if($i == 1){ ends?>
  </tr>
  <?php } //for($i = 0; $i <$osr_head; $i++){ ends?>
</table>