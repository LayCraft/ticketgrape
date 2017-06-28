<!-- Customer Info Layout -->

<?php

/*
Takes _POST variables for Customer info to build a insert or update for the database.	
*/

if (sizeof($_POST)>0){
	print_r($_POST);
	/*
	$type = clean($_POST['custID']);
	$phone = clean($_POST['custPhone']);
	$ext = clean($_POST['custExt']);
	$first = clean($_POST['custFName']);
	$last = clean($_POST['custLName']);
	$address = clean($_POST['custAddress']);
	$email = clean($_POST['custEmail']);
	$zip = clean($_POST['custPostal']);
	*/
}
/*

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
*/
?>
