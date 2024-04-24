<?php

//product_action.php

include('database_connection.php');

include('function.php');


if(isset($_POST['btn_action']))
{
	if($_POST['btn_action'] == 'load_brand')
	{
		echo fill_brand_list($connect, $_POST['category_id']);
	}

	if($_POST['btn_action'] == 'Add')
	{
		$query = "
		INSERT INTO product (category_id, brand_id, product_name, product_description, product_quantity, product_unit, product_base_price, product_tax, product_enter_by, product_status, product_date)
		VALUES (?,?,?,?,?,?,?,?,?,?)
		";
		$statement = $connect->prepare($query);
		$data = array(
			$_POST['category_id'],
			$_POST['brand_id'],
			$_POST['product_name'],
			$_POST['product_description'],
			$_POST['product_quantity'],
			$_POST['product_unit'],
			$_POST['product_base_price'],
			$_POST['product_tax'],
			$_SESSION["user_id"],
			'active',
			date("Y-m-d")
		);
		$statement->bind_param("ssssssssss", ...$data);
		$statement->execute();
		if($statement->affected_rows > 0)
		{
			echo 'Product Added';
		}
	}
	if($_POST['btn_action'] == 'product_details')
	{
		$query = "
		SELECT * FROM product 
		INNER JOIN category ON category.category_id = product.category_id 
		INNER JOIN brand ON brand.brand_id = product.brand_id 
		INNER JOIN user_details ON user_details.user_id = product.product_enter_by 
		WHERE product.product_id = ?
		";
		$statement = $connect->prepare($query);
		$statement->bind_param("s", $_POST["product_id"]);
		$statement->execute();
		$result = $statement->get_result()->fetch_all(MYSQLI_ASSOC);
		$output = '
		<div class="table-responsive">
			<table class="table table-boredered">
		';
		foreach($result as $row)
		{
			$status = '';
			if($row['product_status'] == 'active')
			{
				$status = '<span class="label label-success">Active</span>';
			}
			else
			{
				$status = '<span class="label label-danger">Inactive</span>';
			}
			$output .= '
			<tr>
				<td>Product Name</td>
				<td>'.$row["product_name"].'</td>
			</tr>
			<tr>
				<td>Product Description</td>
				<td>'.$row["product_description"].'</td>
			</tr>
			<tr>
				<td>Category</td>
				<td>'.$row["category_name"].'</td>
			</tr>
			<tr>
				<td>Brand</td>
				<td>'.$row["brand_name"].'</td>
			</tr>
			<tr>
				<td>Available Quantity</td>
				<td>'.$row["product_quantity"].' '.$row["product_unit"].'</td>
			</tr>
			<tr>
				<td>Base Price</td>
				<td>'.$row["product_base_price"].'</td>
			</tr>
			<tr>
				<td>Tax (%)</td>
				<td>'.$row["product_tax"].'</td>
			</tr>
			<tr>
				<td>Enter By</td>
				<td>'.$row["user_name"].'</td>
			</tr>
			<tr>
				<td>Status</td>
				<td>'.$status.'</td>
			</tr>
			';
		}
		$output .= '
			</table>
		</div>
		';
		echo $output;
	}
	if($_POST['btn_action'] == 'fetch_single')
	{
		$query = "
		SELECT * FROM product WHERE product_id = ?
		";
		$statement = $connect->prepare($query);
		$statement->bind_param("i", $_POST["product_id"]);
		$statement->execute();
		$result = $statement->get_result()->fetch_all(MYSQLI_ASSOC);
		$output = array();
		foreach($result as $row)
		{
			$output['category_id'] = $row['category_id'];
			$output['brand_id'] = $row['brand_id'];
			$output["brand_select_box"] = fill_brand_list($connect, $row["category_id"]);
			$output['product_name'] = $row['product_name'];
			$output['product_description'] = $row['product_description'];
			$output['product_quantity'] = $row['product_quantity'];
			$output['product_unit'] = $row['product_unit'];

			$output['product_base_price'] = $row['product_base_price'];
			$output['product_tax'] = $row['product_tax'];
		}
		echo json_encode($output);
	}

	if($_POST['btn_action'] == 'Edit')
	{
		$data = array(
			$_POST['category_id'],
			$_POST['brand_id'],
			$_POST['product_name'],
			$_POST['product_description'],
			$_POST['product_quantity'],
			$_POST['product_unit'],
			$_POST['product_base_price'],
			$_POST['product_tax'],
			$_POST['product_id']
		);
		$query = "
		UPDATE product 
		set category_id = ?, 
		brand_id = ?, 
		product_name = ?,
		product_description = ?, 
		product_quantity = ?, 
		product_unit = ?, 
		product_base_price = ?, 
		product_tax = ? 
		WHERE product_id = ?
		";
		$statement = $connect->prepare($query);
		$statement->bind_param('isssssssi', ...$data);
		$statement->execute();
		if($statement->affected_rows > 0)
		{
			echo 'Product Details Edited';
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
		UPDATE product 
		SET product_status = ? 
		WHERE product_id = ?
		";
		$statement = $connect->prepare($query);
		$statement->bind_param("si", $status, $_POST["product_id"]);
		$statement->execute();
		if($statement->affected_rows > 0)
		{
			echo 'Product status change to ' . $status;
		}
	}
}


?>


