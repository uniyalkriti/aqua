<?php
@session_start();
ob_start();
require_once('../../include/config.inc.php');
require_once(BASE_URI_ROOT . ADMINFOLDER . MSYM . 'include' . MSYM . 'my-functions.php');

	$dealer_id = $_SESSION[SESS.'data']['dealer_id'];
	if(isset($_POST['lid']) && $_POST['lid']!='')
	{
		$beat_id = $_POST['lid'];
		$q = "SELECT retailer.id as id ,concat(retailer.name,' [',location_5.name,']') as `name` from location_5 INNER JOIN retailer ON retailer.location_id = location_5.id where dealer_id='$dealer_id' AND location_5.id=$beat_id order by retailer.name asc";
		$q_ext = mysqli_query($dbc, $q);
		$num = mysqli_num_rows($q_ext);

		$return = '';

		if($num)
		{
			$return .= '<option>==Please Select==</option>';
			while($row = mysqli_fetch_assoc($q_ext))
			{
				$id = $row['id'];
				$name = $row['name'];

				$return .= "<option value='$id'>$name</option>";
			}
		}else{
			$return = false;
		}
		
		echo $return;
	}