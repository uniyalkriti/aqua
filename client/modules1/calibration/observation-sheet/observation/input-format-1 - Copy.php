<?php if (!defined('BASE_URL')) die('direct script access not allowed');?>
<?php 
$osr_head = 8;
$osr_head_value = 10;

?>
<table width="100%" border="1" class="searchlist">
  <tr class="search1tr">
    <!--<td>&nbsp;</td>-->
    <td>S.No.</td>
    <td>UUC IN</td>    
    <td colspan="10" align="center">Observation Reading Values</td>
    <td>Average</td>
  </tr>
  <?php for($i = 0; $i <$osr_head; $i++){?>
  <tr>
    <!--<td><?php arr_pulldown('is_selected[]', array('N','Y'), '', true, false); ?></td>-->
    <td><?php echo $i+1;?></td>
    <td><input type="text" name="osr_head[]" value="" /></td>
    <?php for($j = 0; $j <$osr_head_value; $j++){?>
    <td><input type="text" name="osr_head_value<?php echo $i;?>[]" value="" class="monitormove" /></td>
    <?php } //for($j = 0; $i <$osr_head_value; $j++){ ends?>
    <td><input type="text" name="row_avg[]" value="" class="readonly" readonly="readonly"/></td>
  </tr>
  <?php } //for($i = 0; $i <$osr_head; $i++){ ends?>
</table>