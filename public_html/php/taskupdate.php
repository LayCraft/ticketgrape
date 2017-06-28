TO BE DELETED

<?php
require "../../MrClean.php";
require "database.php";

$type = $_POST[0];

if($type == "save")
{
	$name = clean($_POST[1]);
	$price = clean($_POST[2]);
	$statement = "INSERT INTO taskmenu ()"
} 
elseif ($type == "delete")
{
 	$id = clean($_POST[1]);
	
}
?>