<?php 

	include_once('conn.php');

	// print_r($_POST); die;
	$user_name = $_POST['user_name'];
	$address = $_POST['address'];
	$age = $_POST['age'];
	$mobile_no = $_POST['mobile_no'];
	$first_name = $_POST['first_name'];
	$middle_name = $_POST['middle_name'];
	$last_name = $_POST['last_name'];

	if($_POST['module'] == 'insert')
	{
		$insert_query = "INSERT INTO `users_sample_data`(`user_name`,`first_name`,`middle_name`,`last_name`, `address`, `age`, `mobile_no`, `created_at`, `updated_at`) VALUES ('$user_name','$first_name','$middle_name','$last_name','$address','$age','$mobile_no',NOW(),NOW())";
		$insert_query_run = mysqli_query($dbc, $insert_query);
		$data['code'] = 200;
		return json_encode($data);
	}
	elseif ($_POST['module'] == 'update') {

		$id = $_POST['id'];
		$update_query = "UPDATE `users_sample_data` SET `user_name`='$user_name',`first_name`='$first_name',`middle_name`='$middle_name',`last_name`='$last_name',`address`='$address',`age`='$age',`mobile_no`='$mobile_no',`updated_at`=NOW() WHERE id='$id'";
		$update_query_run = mysqli_query($dbc, $update_query);
		return 0;
	}
	elseif ($_POST['module'] == 'delete') {

		$id = $_POST['id'];
		$update_query = "DELETE from `users_sample_data` WHERE id='$id'";
		$update_query_run = mysqli_query($dbc, $update_query);
		return 0;
	}
	elseif ($_POST['module_select'] == 'edit') {

		$id = $_POST['id'];
		$data = "SELECT * FROM `users_sample_data` WHERE id='$id' LIMIT 1";
		$select_data = mysqli_query($dbc, $data);
		$data_set = mysqli_fetch_object($select_data);
	}
	else
	{
		$data = "SELECT * FROM `users_sample_data`";
		$select_data = mysqli_query($dbc, $data);

	}
?>