<?php

//edit_profile.php

include('database_connection.php');

if(isset($_POST['user_name']))
{
	if($_POST["user_new_password"] != '')
	{

		$old_password= $_POST["o_user_new_password"];
		$query = "SELECT * FROM `user_details` WHERE `user_id` = ? LIMIT 1";
		$statement = $connect->prepare($query);
		$statement->bind_param("i", $_SESSION["user_id"]);
		$statement->execute();
		$row = $statement->get_result()->fetch_assoc();
		
			if(password_verify($old_password,$row['user_password']))
			{
				$query = "
				UPDATE user_details SET 
					user_name = '".$_POST["user_name"]."', 
					user_email = '".$_POST["user_email"]."', 
					user_password = '".password_hash($_POST["user_new_password"], PASSWORD_DEFAULT)."' 
					WHERE user_id = '".$_SESSION["user_id"]."'
				";
			}
			else
			{
				echo '<div class="alert alert-danger">Old password is wrong</div>';
				exit;
			}
		
	}
	else
	{
		$query = "
		UPDATE user_details SET 
			user_name = '".$_POST["user_name"]."', 
			user_email = '".$_POST["user_email"]."'
			WHERE user_id = '".$_SESSION["user_id"]."'
		";
	}
	$statement->execute();
	$result = $statement->get_result();
	$data = $result->fetch_all(MYSQLI_ASSOC);
	if(!empty($data))
	{
		echo '<div class="alert alert-success">Profile Edited</div>';
	}
}

?>
