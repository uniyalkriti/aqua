<?php if (!defined('BASE_URL')) die('direct script access not allowed');?>
<?php
$if = new inputformat();
$edit = isset($heid) ? true :  false;
if(isset($heid))
	$cf = $if->inputvalue($_POST['rifId'], true, $_GET['id']);
else
	$cf = $if->inputvalue($_POST['rifId'], false, $_POST['tmpmasterId']);
?>
<input type="hidden" name="rifId" value="<?php echo $_POST['rifId'];?>" />
<table width="100%" border="0" class="searchlist">
  <tr>
    <td ><input type="text" name="osr_head[]" value="<?php echo $cf[0];?>" /></td>
    <td ><input type="text" name="osr_head_value0[]" value="<?php echo $cf[1];?>" /></td>
  </tr>
  <tr>
    <td colspan="2">UNCERTAINITY AT  <input type="text" style="width:100px;" name="uncertainity" value="<?php if(isset($_POST['uncertainity'])) echo $_POST['uncertainity']; ?>" /> CONFIDENCE LEVEL FOR A COVERAGE FACTOR K = 2</td>
  </tr>
</table>