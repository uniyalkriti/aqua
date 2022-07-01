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
  <?php foreach($cf['osr_head'] as $key=>$value){?>
  <tr>
    <td colspan="4"><input type="text" name="osr_head[]" value="<?php echo $value;?>" /></td>
  </tr>
  <?php for($i=0; $i<count($cf['col'.$key.'1']); $i++){?>
  <?php if($i == 0){?>
  <tr>
    <td><input type="text" name="col<?php echo $key;?>1[]" value="<?php echo $cf['col'.$key.'1'][0];?>" /></td>
    <td><input type="text" name="col<?php echo $key;?>2[]" value="<?php echo $cf['col'.$key.'2'][0];?>" /></td>
    <td><input type="text" name="col<?php echo $key;?>3[]" value="<?php echo $cf['col'.$key.'3'][0];?>" /></td>
    <td><input type="text" name="col<?php echo $key;?>4[]" value="<?php echo $cf['col'.$key.'4'][0];?>" /></td>
  </tr>
  <?php }elseif($i == 1){ //if($i == 1){ ends?>
  <tr>
    <td><input type="text" name="col<?php echo $key;?>1[]" value="<?php echo $cf['col'.$key.'1'][1];?>" /></td>
    <td><input type="text" name="col<?php echo $key;?>2[]" value="<?php echo $cf['col'.$key.'2'][1];?>" /></td>
    <td><input type="text" name="col<?php echo $key;?>3[]" value="<?php echo $cf['col'.$key.'3'][1];?>" /></td>
    <td rowspan="<?php echo count($cf['col'.$key.'1']) - 1;?>"><input type="text" name="col<?php echo $key;?>4[]" value="<?php echo $cf['col'.$key.'4'][1];?>" /></td>
  </tr>  
  <?php }elseif($i > 1){ //elseif($i == 2){ ends){?>
  <tr>
    <td><input type="text" name="col<?php echo $key;?>1[]" value="<?php echo $cf['col'.$key.'1'][$i];?>" /></td>
    <td><input type="text" name="col<?php echo $key;?>2[]" value="<?php echo $cf['col'.$key.'2'][$i];?>" /></td>
    <td><input type="text" name="col<?php echo $key;?>3[]" value="<?php echo $cf['col'.$key.'3'][$i];?>" /></td>
  </tr> 
  <?php }//elseif($i > 2){ ends){
  } // for($i=1; $i<count($cf['col'.$key.$i]); $i++){ ends
  ?> 
  <?php } // foreach($cf['osr_head'] as $key=>$value){ ends ?>
  <tr>
    <td colspan="4">Expanded uncertainty of Measurement is <input type="text" style="width:100px;" name="uncertainity" value="<?php if(isset($_POST['uncertainity'])) echo $_POST['uncertainity']; ?>" /> at coverage factor k=2</td>
  </tr>
</table>