<?php if (!defined('BASE_URL')) die('direct script access not allowed');?>
<?php 
//this will show the rows for xa calculation
function show_xa($readings)
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
    <td>S.No</td>
    <td>X</td>
    <td>Xi</td>
    <td>X-Xi</td>
    <td>(X-Xi)<sup>2</sup></td>
    <td>&sum;(X-Xi)<sup>2</sup></td>
    <td>STD. DEV. (Ua) = &radic;&sum;(X-Xi)<sup>2</sup>/n-1</td>
  </tr>
  <?php if(isset($_POST['submit'])) echo show_xa($_POST['readings']);?>
</table>