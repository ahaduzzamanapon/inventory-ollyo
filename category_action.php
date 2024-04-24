<?php

//category_action.php

include('database_connection.php');

if(isset($_POST['btn_action']))
{
	if($_POST['btn_action'] == 'Add')
	{
		$category_name = $_POST["category_name"];
		$query = "
		INSERT INTO category (category_name) 
		VALUES ('$category_name')
		";
		$statement = $connect->prepare($query);
		if($statement->execute())
		{
			echo 'Category Name Added';
		}
	}
	
	if($_POST['btn_action'] == 'fetch_single')
	{
		$query = "SELECT * FROM category WHERE category_id = ?";
		$statement = $connect->prepare($query);
		$statement->bind_param("i", $_POST["category_id"]);
		$statement->execute();
		$result = $statement->get_result();
		$row = $result->fetch_assoc();
		
		$output['category_name'] = $row['category_name'];
		
		echo json_encode($output);
	}

	if($_POST['btn_action'] == 'Edit')
	{
		$category_name = $_POST["category_name"];
		$query = "
		UPDATE category set category_name = '$category_name'  
		WHERE category_id = ?
		";
		$statement = $connect->prepare($query);
		$statement->bind_param("i", $_POST["category_id"]);
		if($statement->execute())
		{
			echo 'Category Name Edited';
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
		UPDATE category 
		SET category_status = '$status' 
		WHERE category_id = ?
		";
		$statement = $connect->prepare($query);
		$statement->bind_param("i", $_POST["category_id"]);
		if($statement->execute())
		{
			echo 'Category status change to ' . $status;
		}
	}
}

?>
