TO BE DELETED

<?php
/*
Takes _POST variables for Customer info to build a insert or update for the database.	
*/
include "database.php";
include "../../MrClean.php";

$type = clean($_POST['ctype']);
$phone = clean($_POST['cphone']);
$ext = clean($_POST['cext']);
$first = clean($_POST['cfirst']);
$last = clean($_POST['clast']);
$address = clean($_POST['caddress']);
$email = clean($_POST['cemail']);
$zip = clean($_POST['czip']);

$success = FALSE;
//builds the query different for UPDATE or INSERT then sends the query to the database.php
if($type == "update")
{
	$queryString = "UPDATE customer  SET lastname=" . $last .", firstname=" . $first . ", phone=" . $phone. ", ext=" . $ext . ", address=" . $address . ", email=" . $email . ", zip=" . $zip . " WHERE phone=" . $phone . ";";
	$success = insertQuery($queryString);
}
else if ($type == "insert")
{
	$queryString = "INSERT customer (lastname,firstname,phone,ext,address,email,zip) VALUES (" . $last . "," . $first . "," . $phone . "," . $ext . "," . $address . "," . $email . "," . $zip . ");";
	$success = insertQuery($queryString);
}
else
{

}
?>