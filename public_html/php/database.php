<?php
//functionality for querying the database.

// return 2D array of values for multi-column results
// return 1D array of values for single column results
$accesspath = explode("public_html", dirname(__FILE__));
require $accesspath[0]."access.php";

if ($mysqli->connect_errno) {
	echo "MySQL connection failure: ", $mysqli->connect_error;
	exit;
}

function sendQuery($query) {
	global $mysqli;
	$result = $mysqli->query($query);
	if ($result) {
		$array = array();
		while ($row = $result->fetch_row()) {
			// if single-column, don't nest array
			if (count($row)==1) $array[] = $row[0];
			else $array[] = $row;
		} 
		return $array;
	} else {
		return $mysqli->error;
	}
	
}

// returns PK value of the inserted row, or FALSE on failure.
function insertQuery($query) {
	global $mysqli;
	$result = $mysqli->query($query);
	if ($result) {
		$incremPK = $mysqli->insert_id;
		if ($incremPK > 0) {
			return $incremPK;
		} else {
			return 'success';
		}
	} else {
		return $mysqli->error;
	}
}

?>
