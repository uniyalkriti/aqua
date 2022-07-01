<?php if (!defined('BASE_URL')) die('direct script access not allowed');?>
<?php 
//this will show the rows for xa calculation
function show_xa1($readings)
{
	$readings = explode(',', $readings);
	$x_xi = $x_xi_square =array();
	foreach($readings as $key=>$value)
		$readings[$key] = trim($value);
	$output = '';
	$totalread =  count($readings);
	if($totalread < 2)
		return '<tr><td colspan="7"><span class="warn">Please enter minimum <strong>2 readings</strong></span></td></tr>';
	$meanvalue = array_sum($readings)/$totalread;
	foreach($readings as $key=>$value){
		$diff = $value-$meanvalue;
		$x_xi[$key] = $diff;
		$x_xi_square[$key] = pow($diff,2);
	}
	$x_xi_square_sum = array_sum($x_xi_square);
	$std_dev = sqrt(($x_xi_square_sum/($totalread-1)));
	$std_dev = sprintf ("%.4f", $std_dev);
	
	foreach($readings as $key=>$value){
		$output .= '<tr>';
		$output .= '  <td>'.($key+1).'</td>';
		$output .= '  <td>'.$value.'</td>';
		if($key == 0)
			$output .= '  <td rowspan="'.$totalread.'">'.$meanvalue.'</td>';
		$output .= '  <td>'.$x_xi[$key].'</td>';
		$output .= '  <td>'.$x_xi_square[$key].'</td>';	
		if($key == 0)
			$output .= '  <td rowspan="'.$totalread.'">'.$x_xi_square_sum.'</td>';
		if($key == 0)
			$output .= '  <td rowspan="'.$totalread.'">'.$std_dev.'</td>';
		$output .= '</tr>';	
	}// foreach($readings as $key=>$value){ ends
	$output .= '<tr>';
	$output .= '  <td colspan="4" style="font-weight:bold;">Standard Deviation of Mean(U<sub>A</sub>) = (STD.DEV./&radic;n)</td>';
	$output .= '  <td>'.$std_dev.'/&radic;'.($totalread).'</td>';
	$n_1_sqrt = sprintf ("%.4f", sqrt($totalread));
	$output .= '  <td>'.$std_dev.'/'.$n_1_sqrt.'</td>';
	$ua = round(($std_dev/$n_1_sqrt),4);
	$output .= '  <td><strong>'.$ua.'</strong><input type="hidden" name="ua_calc" id="ua_calc" value="'.$ua.'"></td>';
	return $output;
}
?>
<table width="100%" class="valigntop" >
  <tr style="font-weight:bold;">
    <td>Symbol & distibution</td>
    <td>Uncertainity Component</td>
    <td>Normal : Divisor 2</td>
    <td>Rectangular : Divisor 3</td>
    <td>Uncertainity Value</td>
  </tr>
  <tr>
    <td>U1</td>
    <td>Std.1 Unc. due to Calibration Certificate</td>
    <td><input type="text" name="myuncertain[]" value="" /></td>
    <td>&nbsp;</td>
    <td><input type="text" name="myuvalue[]" value="" /></td>    
  </tr>
  <tr>
    <td>U2</td>
    <td>Std.1 Unc. due to accuracy or Acceptance Criteria</td>
    <td>&nbsp;</td>
    <td><input type="text" name="myaccuracy" value="" /></td>    
    <td><input type="text" name="myuvalue[]" value="" /></td>    
  </tr>
  <tr>
    <td>U3</td>
    <td>Std.2 Unc. due to Calibration Certificate</td>
    <td><input type="text" name="myuncertain[]" value="" /></td>
    <td>&nbsp;</td>
    <td><input type="text" name="myuvalue[]" value="" /></td>    
  </tr>
  <tr>
    <td>U4</td>
    <td>Std.2 Unc. due to accuracy or Acceptance Criteria</td>
    <td>&nbsp;</td>
    <td><input type="text" name="myaccuracy" value="" /></td>    
    <td><input type="text" name="myuvalue[]" value="" /></td>    
  </tr>
  <tr>
    <td>U5</td>
    <td>Std.3 Unc. due to Calibration Certificate</td>
    <td><input type="text" name="myuncertain[]" value="" /></td>
    <td>&nbsp;</td>
    <td><input type="text" name="myuvalue[]" value="" /></td>    
  </tr>
  <tr>
    <td>U6</td>
    <td>Std.3 Unc. due to accuracy or Acceptance Criteria</td>
    <td>&nbsp;</td>
    <td><input type="text" name="myaccuracy" value="" /></td>    
    <td><input type="text" name="myuvalue[]" value="" /></td>    
  </tr>
  <tr>
    <td>U7</td>
    <td>Uncertainity due to wringing effect</td>
    <td>&nbsp;</td>
    <td><input type="text" name="myu7" id="myu7" value="" /></td>    
    <td><input type="text" name="myuvalue[]" value="" /></td>    
  </tr>
  <tr>
    <td>U8</td>
    <td>Resolution of UUC  <?php if(isset($_POST['resolution'])) echo $_POST['resolution'];?> x resolution</td>
    <td>&nbsp;</td>
    <td><input type="text" name="myu8" id="myu8" value="" /></td>    
    <td><input type="text" name="myuvalue[]" value="" /></td>    
  </tr>
  <tr>
    <td>U9</td>
    <td>Room Temp. Variation during calibration</td>
    <td>&nbsp;</td>
    <td><input type="text" name="myu9" id="myu9" value="" /></td>    
    <td><input type="text" name="myuvalue[]" value="" /></td>    
  </tr>
  <tr>
    <td>U10</td>
    <td>Temp. difference between UUC & STD.</td>
    <td>&nbsp;</td>
    <td><input type="text" name="myu10" id="myu10" value="" /></td>    
    <td><input type="text" name="myuvalue[]" value="" /></td>    
  </tr>
  <tr>
    <td>U11</td>
    <td>Unc.due to Difference in thermal coefficient of expansion of UUC & STD.</td>
    <td>&nbsp;</td>
    <td><input type="text" name="myu11" id="myu11" value="" /></td>    
    <td><input type="text" name="myuvalue[]" value="" /></td>    
  </tr>
  <tr>
    <td>U12</td>
    <td>Unc.due to Difference in thermal coefficient of expansion of STD.</td>
    <td>&nbsp;</td>
    <td><input type="text" name="myu12" id="myu12" value="" /></td>    
    <td><input type="text" name="myuvalue[]" value="" /></td>    
  </tr>
  <tr>
    <td>U13</td>
    <td>Unc. Due to temp. indication device Uncertainty </td>
    <td>&nbsp;</td>
    <td><input type="text" name="myu13" id="myu13" value="" /></td>    
    <td><input type="text" name="myuvalue[]" value="" /></td>    
  </tr>
  <tr>
    <td>U14</td>
    <td>Unc. Due to temp. indication device deviation  </td>
    <td>&nbsp;</td>
    <td><input type="text" name="myu14" id="myu14" value="" /></td>    
    <td><input type="text" name="myuvalue[]" value="" /></td>    
  </tr>
  <tr>
    <td><strong>U<sub>b</sub></strong></td>
    <td colspan="3"> =  &radic; (U<sub>1</sub>)<sup>2</sup> + (U<sub>2</sub>)<sup>2</sup> _ _ _ (U<sub>n</sub>)<sup>2</sup> </td>    
    <td><input type="text" name="myubfinal" value="" /></td>    
  </tr>
  <tr>
    <td><strong>U<sub>c</sub></strong></td>
    <td colspan="3"> =  &radic; (U<sub>A</sub>)<sup>2</sup> + (U<sub>B</sub>)<sup>2</sup></td>    
    <td><input type="text" name="myucfinal" value="" /></td>    
  </tr>
  <tr>
    <td><strong>Coverage Factor k</strong></td>
    <td colspan="3">&nbsp;</td>    
    <td><input type="text" name="coveragefactor" value="" /></td>    
  </tr>
  <tr>
    <td><strong>Expanded Uncertainity U<sub>e</sub></strong></td>
    <td colspan="3"> =  U<sub>c</sub>*k</td>    
    <td><input type="text" name="myuefinal" value="" /></td>    
  </tr>
  <tr>
    <td colspan="4" align="right"><strong>Final Result</strong></td>
    <td>&plusmn;<input style="width:70px;" type="text" name="myuncertainityfinal" value="" readonly="readonly" class="readonly" />microns</td>    
  </tr>
  <?php //if(isset($_POST['submit'])) echo show_xa($_POST['readings']);?>
</table>