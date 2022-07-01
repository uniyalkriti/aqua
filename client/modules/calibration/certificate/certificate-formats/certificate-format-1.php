<?php if (!defined('BASE_URL')) die('direct script access not allowed');?>
<?php
$trows = 12;
$if = new inputformat();
$edit = isset($heid) ? true :  false;
if(isset($heid))
	$cf = $if->inputvalue($_POST['rifId'], true, $_GET['id']);
else
	$cf = $if->inputvalue($_POST['rifId'], false, $_POST['tmpmasterId'], $_POST['srfItemId']);
//pre($cf);

//allowing the user to change the template values that were left empty
for($i=0; $i<6; $i++){
	if(!isset($cf['osr_head'][$i])) $cf['osr_head'][$i] = '';	
	if(!isset($cf['osr_head_value'.$i])) $cf['osr_head_value'.$i] = '';	
}
for($i=0; $i<$trows; $i++){
	if(!isset($cf['osr_head_value0'][$i])) $cf['osr_head_value0'][$i] = '';
	if(!isset($cf['osr_head_value1'][$i])) $cf['osr_head_value1'][$i] = '';
	if(!isset($cf['osr_head_value2'][$i])) $cf['osr_head_value2'][$i] = '';
	if(!isset($cf['osr_head_value3'][$i])) $cf['osr_head_value3'][$i] = '';
	if(!isset($cf['osr_head_value4'][$i])) $cf['osr_head_value4'][$i] = '';
	if(!isset($cf['osr_head_value5'][$i])) $cf['osr_head_value5'][$i] = '';
}	
?>
<input type="hidden" name="rifId" value="<?php echo $_POST['rifId'];?>" />
<table width="100%" border="1" class="searchlist" >
	<tr>
	<?php foreach($cf['osr_head'] as $key=>$value){?>
    	<td><input type="text" name="osr_head[]" value="<?php echo $value;?>" /></td>
    <?php } ?>
    </tr>
	<?php for($i=0; $i< count($cf['osr_head_value0']); $i++){?>
    <tr>	
      <?php for($j=0; $j< count($cf['osr_head']); $j++){?>
      <td><input type="text" name="osr_head_value<?php echo $j; ?>[]" value="<?php echo $cf['osr_head_value'.$j][$i]?>" /></td>
       <?php } ?>
    </tr>    
    <?php } ?>
</table>