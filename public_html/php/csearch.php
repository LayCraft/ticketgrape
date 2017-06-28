TO BE DELETED

<?php
/*  Customer search script:
	Performs a select..from..where..like query
	For use with AJAX requests via POST
	Required input: search = search term<string>,
					target = target column<JSON array>,
					columns = return columns>JSON array>
	Returns: JSON encoded results formatted as:
			   for Single-column results: enumerated array of values
			   for Multi-column results: 2d array[row][column] */

include "database.php";
include "../../MrClean.php";

$search = clean($_POST['input']);
$target = $_POST['by'];
$columns = $_POST['cols'];

$select = join(",",$columns);
$where = $target;
$query = "select $select from ticket left join customer using (custno) where $where like '$search%'";

$array = sendQuery($query);
echo json_encode($array);

?>

