<?php

//user_action.php

include('database_connection.php');

if(isset($_POST['btn_action']))
{
	if($_POST['btn_action'] == 'Add')
	{
		$query = "INSERT INTO user_details (user_email, user_password, user_name, user_type, user_status)VALUES (?,?,?,?,?)";
		$statement = $connect->prepare($query);
		$user_email = $_POST["user_email"];
		$user_password = $_POST["user_password"];
		$user_name = $_POST["user_name"];
		$user_type = 'user';
		$user_status = 'active';
		$statement->bind_param("sssss",$user_email,$user_password,$user_name,$user_type,$user_status);
		$statement->execute();
		$result = $statement->affected_rows;
		if($result > 0)
		{
			echo 'New User Added';
		}

	}
	if($_POST['btn_action'] == 'fetch_single')
	{
		$query = "
		SELECT * FROM user_details WHERE user_id = ?
		";
		$statement = $connect->prepare($query);
		$statement->bind_param("i", $_POST["user_id"]);
		$statement->execute();
		$result = $statement->get_result();
		$row = $result->fetch_assoc();
		
		$output['user_email'] = $row['user_email'];
		$output['user_name'] = $row['user_name'];
		
		echo json_encode($output);
	}
	if($_POST['btn_action'] == 'Edit')
	{
		if($_POST['user_password'] != '')
		{
			$query = "
			UPDATE user_details SET 
				user_name = ?, 
				user_email = ?,
				user_password = ? 
				WHERE user_id = ?
			";
			$statement = $connect->prepare($query);
			$statement->bind_param("sssi", $_POST["user_name"], $_POST["user_email"], $_POST["user_password"], $_POST["user_id"]);
		}
		else
		{
			$query = "
			UPDATE user_details SET 
				user_name = ?, 
				user_email = ?
				WHERE user_id = ?
			";
			$statement = $connect->prepare($query);
			$statement->bind_param("ssi", $_POST["user_name"], $_POST["user_email"], $_POST["user_id"]);
		}
		$statement->execute();
		$result = $statement->affected_rows;
		if($result > 0)
		{
			echo 'User Details Edited';
		}
	}
	if($_POST['btn_action'] == 'delete')
	{
		$status = 'Active';
		if($_POST['status'] == 'Active')
		{
			$status = 'Inactive';
		}
		$query = "
		UPDATE user_details 
		SET user_status = ? 
		WHERE user_id = ?
		";
		$statement = $connect->prepare($query);
		$statement->bind_param("si", $status, $_POST["user_id"]);
		$statement->execute();
		$result = $statement->affected_rows;
		if($result > 0)
		{
			echo 'User Status change to ' . $status;
		}
	}
}

?>


