<?php





//brand_action.php

include('database_connection.php');

if(isset($_POST['btn_action']))
{
	if($_POST['btn_action'] == 'Add')
	{
		$query = "
		INSERT INTO brand (category_id, brand_name) 
		VALUES (?, ?)
		";
		$statement = $connect->prepare($query);
		$statement->bind_param("is", $_POST["category_id"], $_POST["brand_name"]);
		if($statement->execute())
		{
			echo 'Brand Name Added';
		}
	}

	if($_POST['btn_action'] == 'fetch_single')
	{
		$query = "
		SELECT * FROM brand WHERE brand_id = ?
		";
		$statement = $connect->prepare($query);
		$statement->bind_param("i", $_POST["brand_id"]);
		$statement->execute();
		$result = $statement->get_result()->fetch_assoc();
		$output['category_id'] = $result['category_id'];
		$output['brand_name'] = $result['brand_name'];
		echo json_encode($output);
	}
	if($_POST['btn_action'] == 'Edit')
	{
		$query = "
		UPDATE brand set 
		category_id = ?, 
		brand_name = ? 
		WHERE brand_id = ?
		";
		$statement = $connect->prepare($query);
		$statement->bind_param("isi", $_POST["category_id"], $_POST["brand_name"], $_POST["brand_id"]);
		if($statement->execute())
		{
			echo 'Brand Name Edited';
		}
	}

	if($_POST['btn_action'] == 'delete')
	{
		$status = 'active';
		if($_POST['status'] == 'active')
		{
			$status = 'inactive';
		}
		$query = "
		UPDATE brand 
		SET brand_status = ? 
		WHERE brand_id = ?
		";
		$statement = $connect->prepare($query);
		$statement->bind_param("si", $status, $_POST["brand_id"]);
		if($statement->execute())
		{
			echo 'Brand status change to ' . $status;
		}
	}
}

?>
