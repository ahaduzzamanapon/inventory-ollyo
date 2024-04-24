<?php

//category_fetch.php

include('database_connection.php');

$query = '';

$output = array();
$query .= "
SELECT * FROM category ";

if(isset($_POST["search"]["value"]))
{
    $query .= 'WHERE category_name LIKE "%'.$_POST["search"]["value"].'%" ';
    $query .= 'OR category_status LIKE "%'.$_POST["search"]["value"].'%" ';
}

if(isset($_POST["order"]))
{
	$query .= 'ORDER BY ';
	if ($_POST['order']['0']['column'] == 0) {
        $query .= 'category_id'.' '.$_POST['order']['0']['dir'].' ';
    }else{
        $query .= $_POST['order']['0']['column'].' '.$_POST['order']['0']['dir'].' ';
    }}
else
{
    $query .= 'ORDER BY category_id ASC ';
}

if(isset($_POST["length"]) && $_POST["length"] != -1)
{
    $query .= 'LIMIT ' . $_POST['start'] . ', ' . $_POST['length'];
}

$statement = $connect->prepare($query);

$statement->execute();

$result = $statement->get_result();

$data = array();

$filtered_rows = $statement->num_rows;

while($row = $result->fetch_assoc())
{
    $status = '';
    if($row['category_status'] == 'active')
    {
        $status = '<span class="label label-success">Active</span>';
    }
    else
    {
        $status = '<span class="label label-danger">Inactive</span>';
    }
    $sub_array = array();
    $sub_array[] = $row['category_id'];
    $sub_array[] = $row['category_name'];
    $sub_array[] = $status;
    $sub_array[] = '<button type="button" name="update" id="'.$row["category_id"].'" class="btn btn-warning btn-xs update">Update</button>';
    $sub_array[] = '<button type="button" name="delete" id="'.$row["category_id"].'" class="btn btn-danger btn-xs delete" data-status="'.$row["category_status"].'">Delete</button>';
    $data[] = $sub_array;
}

$output = array(
    "draw"          =>  intval($_POST["draw"]),
    "recordsTotal"      =>  $filtered_rows,
    "recordsFiltered"   =>  $filtered_rows,
    "data"              =>  $data
);

echo json_encode($output);


?>

