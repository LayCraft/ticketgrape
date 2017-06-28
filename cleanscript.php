<?php
/*
Curtis LayCraft

This file needs ../../access.php
It recieves three string varibles representing parts of a simple mysql query.
This file contains functions to sanitize the array. It returns an associative
array that is structured generally like this:

Array(
	titles: Array([0]=>first_name [1]=>last_name
	values: Array(
		[0]=> Array([first_name]=>"Firsty" [last_name]=>"lasty" …) 
		[1]=> Array([first_name]=>"Firsty" [last_name]=>"lasty" …)
		...
	)
*/


// Kill the script if a semicolon, SQL comment, or column alias markers exist.
function charCheck($string){
	preg_match("/(--)|\;|\"/",$string,$nullString);
	if ($nullString != null){
		die("…something went wrong. You cannot input  
	 	semicolons, SQL comments, or double quotes. <br/>Please try again.");
	}else{
	return $string;
	}
}
// check if the "where" field is null. If it is, it is omitted.
function whereCheck($where){
	if ($where==null){
		return $where;
	} else {
		// append the where postfix
		$where = "WHERE $where";
		return $where;
	}
}

function query($select,$from,$where){
	// make $LinkID to the database
	require '../../access.php';

	// Clean all the values posted.
	$select = strip_tags(charCheck($select));
	$from = strip_tags(charCheck($from));
	$where = strip_tags(whereCheck(charCheck($where)));

	// SET A DEFAULT QUERY
	if(empty($select)){
		$select = '*';		
	}
	if(empty($from)){
		$from = 'employees';
	}

	// assemble the input info to SQL
	$query =  "SELECT $select FROM $from $where";
	$result = mysqli_query($LinkID, $query);
	//uncomment for debugging
	//echo mysqli_error($LinkID);



	//parse the result into an associative array
	$things=mysqli_fetch_assoc($result);
	
	
	foreach (array_keys($things) as $key){
		$keys[]= $key;
	}
	
	//while there are rows of data write a new value to 
	while ($things=mysqli_fetch_assoc($result)) {	
		$values[] = $things;
	}

	//append the keys to the array
	$list['titles'] = $keys;
	
	//append the values onto the end of the associative array
	$list['values'] = $values;

	//return the result
	return $list;
}
?>