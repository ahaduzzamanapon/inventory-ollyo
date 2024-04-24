<?php

//product_fetch.php

include('database_connection.php');
include('function.php');

$column = array('product_id', 'category_name', 'brand_name', 'product_name', 'product_quantity', 'user_name', 'product_status', null, null, null);

$query = "
SELECT * FROM product 
INNER JOIN brand ON brand.brand_id = product.brand_id
INNER JOIN category ON category.category_id = product.category_id 
INNER JOIN user_details ON user_details.user_id = product.product_enter_by 
";

if(isset($_POST["search"]["value"]))
{
	$query .= 'WHERE brand.brand_name LIKE "%'.$_POST["search"]["value"].'%" ';
	$query .= 'OR category.category_name LIKE "%'.$_POST["search"]["value"].'%" ';
	$query .= 'OR product.product_name LIKE "%'.$_POST["search"]["value"].'%" ';
	$query .= 'OR product.product_quantity LIKE "%'.$_POST["search"]["value"].'%" ';
	$query .= 'OR user_details.user_name LIKE "%'.$_POST["search"]["value"].'%" ';
	$query .= 'OR product.product_id LIKE "%'.$_POST["search"]["value"].'%" ';
}

if(isset($_POST['order']))
{
	$query .= 'ORDER BY ';
	if ($_POST['order']['0']['column'] == 0) {
        $query .= 'product_id'.' '.$_POST['order']['0']['dir'].' ';
    }else{
        $query .= $_POST['order']['0']['column'].' '.$_POST['order']['0']['dir'].' ';
    }
}
else
{
	$query .= 'ORDER BY product_id ASC ';
}

$limit = '';
if(isset($_POST["length"]) && $_POST["length"] != -1)
{
	$limit = 'LIMIT ' . $_POST['start'] . ', ' . $_POST['length'];
}

$statement = $connect->prepare($query . $limit);

$statement->execute();

$result = $statement->get_result();

$data = array();

$filtered_rows = $statement->num_rows;
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
	$sub_array = array();
	$sub_array[] = $row['product_id'];
	$sub_array[] = $row['category_name'];
	$sub_array[] = $row['brand_name'];
	$sub_array[] = $row['product_name'];
	$sub_array[] = available_product_quantity($connect, $row["product_id"]) . ' ' . $row["product_unit"];
	$sub_array[] = $row['user_name'];
	$sub_array[] = $status;
	$sub_array[] = '<button type="button" name="view" id="'.$row["product_id"].'" class="btn btn-info btn-xs view">View</button>';
	$sub_array[] = '<button type="button" name="update" id="'.$row["product_id"].'" class="btn btn-warning btn-xs update">Update</button>';
	$sub_array[] = '<button type="button" name="delete" id="'.$row["product_id"].'" class="btn btn-danger btn-xs delete" data-status="'.$row["product_status"].'">Delete</button>';
	$data[] = $sub_array;
}

function get_total_all_records($connect)
{
	$statement = $connect->prepare('SELECT * FROM product');
	$statement->execute();
	return $statement->num_rows;
}

$output = array(
	"draw"    			=> 	intval($_POST["draw"]),
	"recordsTotal"  	=>  $filtered_rows,
	"recordsFiltered" 	=> 	get_total_all_records($connect),
	"data"    			=> 	$data
);

echo json_encode($output);

?>

